<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Subscription;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findUsers(Event $event)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT
        u.first_name AS firstname,
        u.last_name AS name,
        u.birth_date AS birthdate,
        s.subs_date AS subsdate,
        s.validation_state AS validationstate
        FROM user u
        INNER JOIN subscription s ON u.id = s.user_id
        INNER JOIN event e ON e.id = s.event_id
        WHERE e.id = :eventId
        ORDER BY s.subs_date ASC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['eventId' => $event->getId()]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function findOne($user, $event)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.event = :event')
            ->setParameters(array('user'=> $user, 'event' => $event))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // These functions count all the subscriptions to an event before our user's.
    // The first one should not be called if the user is not subscribed to this event.
    public function countOrder($user, $event)
    {
        $subs = $this->findOne($user, $event);
        if(is_null($subs))
        {
            return NULL;
        }
        $date = $subs->getSubsDate();
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.user)')
            ->andWhere('s.subsDate < :date')
            ->andWhere('s.event = :event')
            ->setParameters(array('date'=> $date, 'event' => $event))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function countOrderSub($subs)
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.user)')
            ->andWhere('s.subsDate < :date')
            ->andWhere('s.event = :event')
            ->setParameters(array('date'=> $subs->getSubsDate(), 'event' => $subs->getEvent()))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // This function find max 100 subscriptions to an event after our user's.
    public function findAfter($subs)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.subsDate > :date')
            ->andWhere('s.event = :event')
            ->setParameters(array('date'=> $subs->getSubsDate(), 'event' => $subs->getEvent()))
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    // This function returns all subscriptions at future events pertaining to one user.
    public function findByUser($user, EventRepository $repo)
    {
        $events = $repo->findAllNext();
        return $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.event IN (:events)')
            ->setParameters(array('user'=> $user, 'events' => $events))
            ->getQuery()
            ->getResult()
        ;
    }

    // This function returns all subscriptions to one event.
    public function findByEvent($event)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.event = :event')
            ->setParameter('event', $event)
            ->orderBy('s.subsDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // This function returns all subscriptions with validationState = false (no licence) to one event.
    public function findByEventLicence($event)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.event = :event')
            ->andWhere('s.validationState = 0')
            ->setParameter('event', $event)
            ->orderBy('s.subsDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
