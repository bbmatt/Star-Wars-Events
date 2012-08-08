<?php

namespace Yoda\EventBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
    public function getUpcomingEvents()
    {
        return $this
            ->createQueryBuilder('e')
            ->orderBy('e.time', 'ASC')
            ->andWhere('e.time > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->execute()
        ;
    }

    public function findRecentlyUpdatedEvents()
    {
        return $this
            ->createQueryBuilder('e')
            ->andWhere('e.updated > :since')
            ->setParameter('since', new \DateTime('24 hours ago'))
            ->getQuery()
            ->execute()
        ;
    }
}