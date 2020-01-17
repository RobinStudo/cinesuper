<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    
    public function findByDate()
    {
        //creer une variable date
        $today= new \DateTime();
        
        //recuperer les infos dans la bdd avec le doctrine query language
        return $this->createQueryBuilder('e')
            ->andWhere('e.startEvent <=:now')
            ->andWhere('e.endEvent >=:now')
            ->setParameter('now', $today->format('y/m/d'))
            ->getQuery()
            ->getResult()
        ;
    }
  

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


         $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT e
            FROM App\Entity\Event e
            WHERE e.start_event >= :date 
            And e.end_event <= :date'
        )->setParameter('date', $now);

        return $query->getResult();

    */
}
