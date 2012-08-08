<?php

namespace Yoda\EventBundle\Controller;

use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Form\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
     *
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('EventBundle:Event')
            ->getUpcomingEvents()
        ;

        return array(
            'entities' => $entities
        );
    }

    /**
     * Finds and displays a Event entity.
     *
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EventBundle:Event')
            ->findOneBy(array('slug' => $slug))
        ;

        if (!$entity) {
            throw new \Yoda\EventBundle\Exception\EventNotFoundHttpException();
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('EventBundle:Event:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Event entity.
     *
     */
    public function newAction()
    {
        $securityContext = $this->get('security.context');

        $entity = new Event();
        $form   = $this->createForm(new EventType(), $entity);

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Event entity.
     *
     */
    public function createAction()
    {
        $entity  = new Event();
        $request = $this->getRequest();
        $form    = $this->createForm(new EventType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            // works
            $entity->setOwner($this->getUser());

            // would not work all on its own
            $events = $this->getUser()->getEvents();
            $events[] = $entity;
            $this->getUser()->setEvents($events);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('slug' => $entity->getSlug())));
            
        }

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->checkOwnerSecurity($entity);

        $editForm = $this->createForm(new EventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Event entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->checkOwnerSecurity($entity);

        $editForm   = $this->createForm(new EventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('id' => $id)));
        }

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('EventBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $this->checkOwnerSecurity($entity);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    public function attendAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        /** @var $event \Yoda\EventBundle\Entity\Event */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event for id '.$id);
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createAttendingJsonResponse(true);
        }

        return $this->redirect($this->generateUrl('event_show', array(
            'slug' => $event->getSlug(),
        )));
    }

    public function unattendAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        /** @var $event \Yoda\EventBundle\Entity\Event */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event for id '.$id);
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        if ($this->getRequest()->getRequestFormat() == 'json') {
            return $this->createAttendingJsonResponse(false);
        }

        return $this->redirect($this->generateUrl('event_show', array(
            'slug' => $event->getSlug(),
        )));
    }

    /**
     * @Template("EventBundle:Event:_events.html.twig")
     * @return array
     */
    public function _upcomingEventsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('EventBundle:Event')
            ->getUpcomingEvents()
        ;

        return array(
            'entities' => $entities,
        );
    }

    private function createAttendingJsonResponse($attending)
    {
        $data = array('attending' => $attending);

        $response = new Response(json_encode($data));

        return $response;
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    private function checkOwnerSecurity(Event $event)
    {
        if ($this->getUser() != $event->getOwner()) {
            throw new AccessDeniedException('Not the owner');
        }
    }
}
