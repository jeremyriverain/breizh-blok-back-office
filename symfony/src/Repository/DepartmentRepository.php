<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Department|null find($id, $lockMode = null, $lockVersion = null)
 * @method Department|null findOneBy(array $criteria, array $orderBy = null)
 * @method Department[]    findAll()
 * @method Department[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * 
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<\App\Entity\Municipality>
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public static function findDepartementsWhichHaveAtLeastOneBoulder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin(sprintf('%s.municipalities', $rootAlias), 'municipalities');
        $queryBuilder->innerJoin('municipalities.boulderAreas', 'boulderAreas');
        $queryBuilder->innerJoin('boulderAreas.rocks', 'rocks');
        $queryBuilder->innerJoin('rocks.boulders', 'boulders', conditionType: 'WITH', condition: 'boulders.isDisabled = false');

        return $queryBuilder;
    }

}
