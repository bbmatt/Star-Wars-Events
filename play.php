<?php
umask(0000);

require_once __DIR__.'/app/bootstrap.php.cache';
require_once __DIR__.'/app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$kernel->boot();

$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', Request::createFromGlobals());

// all the setup is done!!!!

$em = $container->get('doctrine')
    ->getEntityManager()
;

/** @var $user \Yoda\UserBundle\Entity\User */
$user = $em
    ->getRepository('UserBundle:User')
    ->findOneBy(array('username' => 'user'))
;

/** @var $event \Yoda\EventBundle\Entity\Event */
$event = $user = $em
    ->getRepository('EventBundle')
    ->findOneBy(array('name' => 'Rebellion Fundraiser Bake Sale!'))
;

// works!!!!
$event->setOwner($user);

// does nothing! :(
$events = $user->getEvents();
$events[] = $event;
$user->setEvents($events);

$em->persist($event);
$em->persist($user);
$em->flush();







