<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function countUsers(Event $event)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT
        e.date AS date,
        e.name AS name,
        e.type AS type,
        COUNT(u.id) AS nbusers
        FROM user u
        INNER JOIN subscription s ON u.id = s.user_id
        INNER JOIN event e ON e.id = s.event_id
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['eventId' => $event->getId()]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
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
