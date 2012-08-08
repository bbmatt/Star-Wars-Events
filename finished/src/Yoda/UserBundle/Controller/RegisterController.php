<?php

namespace Yoda\UserBundle\Controller;

use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $defaultUser = new User();
        $defaultUser->setUsername('foo');

        $form = $this->createForm(new RegisterFormType(), $defaultUser);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $user = $form->getData();

                $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                $request->getSession()->setFlash('success', 'Registration went super well!');

                $url = $this->generateUrl('event');

                // authenticates the user right now
                $this->authenticateUser($user);

                return $this->redirect($url);
            }
        }

        return array('form' => $form->createView());
    }

    private function encodePassword($user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user)
        ;

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->getSecurityContext()->setToken($token);
    }
}