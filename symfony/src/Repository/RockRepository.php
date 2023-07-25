<?php

namespace App\Repository;

use App\Entity\Rock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rock[]    findAll()
 * @method Rock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * 
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<\App\Entity\Rock>
 */
class RockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rock::class);
    }

    // /**
    //  * @return Rock[] Returns an array of Rock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rock
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
