<?php

namespace App\Repository;

use App\Entity\Boulder;
use App\Entity\Media;
use App\Entity\Rock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Media[]    findByRockAndBoulder(Rock $rock, Boulder $boulder)
 * 
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<\App\Entity\Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * @return Media[] Returns an array of Media objects
     */
    public function findByRockAndBoulder(Rock $rock, Boulder $boulder)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.rock = :rock')
            ->setParameter('rock', $rock)
            ->leftJoin('m.lineBoulders', 'lineBoulders')
            ->addSelect('lineBoulders')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Media
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
