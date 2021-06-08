<?php

namespace App\Repository;

use App\Entity\Event;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAllByDateDesc()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFourNext()
    {
        $date = new DateTime('now');
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT
            *,
            (
            SELECT
            COUNT(s.user_id)
            FROM subscription s
            WHERE s.event_id = e.id
            ) AS nbusers
        FROM event e
        WHERE e.date > :now
        ORDER BY e.date ASC
        LIMIT 4
        ';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['now' => $date->format('Y-m-d')]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    public function findNext()
    {
        $date = new DateTime('now');
        $nextDate = new DateTime('now');
        $nextDate->add(new DateInterval('P1M'));
        return $this->createQueryBuilder('e')
            ->leftJoin('e.subscriptions', 's', Expr\Join::WITH, 's.event = e.id')
            ->addSelect('COUNT(s.user) AS nbusers')
            ->andWhere('e.date >= :date')
            ->andWhere('e.date < :nextDate')
            ->andWhere('e.type = 1')
            ->setParameters(array('date'=> $date, 'nextDate' => $nextDate))
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findNextKid()
    {
        $date = new DateTime('now');
        $nextDate = new DateTime('now');
        $nextDate->add(new DateInterval('P1M'));
        return $this->createQueryBuilder('e')
            ->leftJoin('e.subscriptions', 's', Expr\Join::WITH, 's.event = e.id')
            ->addSelect('COUNT(s.user) AS nbusers')
            ->andWhere('e.date >= :date')
            ->andWhere('e.date < :nextDate')
            ->andWhere('e.type = 0')
            ->setParameters(array('date'=> $date, 'nextDate' => $nextDate))
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getSingleResult()
        ;
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
