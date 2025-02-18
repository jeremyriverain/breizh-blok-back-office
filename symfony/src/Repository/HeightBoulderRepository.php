<?php

namespace App\Repository;

use App\Entity\HeightBoulder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HeightBoulder>
 *
 * @method HeightBoulder|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeightBoulder|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeightBoulder[]    findAll()
 * @method HeightBoulder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeightBoulderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeightBoulder::class);
    }

    //    /**
    //     * @return HeightBoulder[] Returns an array of HeightBoulder objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HeightBoulder
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
