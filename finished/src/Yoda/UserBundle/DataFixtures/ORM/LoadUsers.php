<?php

namespace Yoda\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Yoda\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadUsers extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function load($manager)
    {
        $user = new User();
        $user->setUsername('user');
        $user->setPlainPassword('user');
        $user->setEmail('user@user.com');
        $manager->persist($user);

        // pass to other fixtures
        $this->addReference('user-user', $user);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPlainPassword('admin');
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setEmail('admin@admin.com');
        $manager->persist($admin);

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getOrder()
    {
        return 10;
    }
}