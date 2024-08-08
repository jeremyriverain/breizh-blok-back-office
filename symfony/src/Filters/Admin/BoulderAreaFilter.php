<?php

namespace App\Filters\Admin;

use App\Form\FilterType\BoulderAreaFilterType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;

class BoulderAreaFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, ?string  $label = 'Boulder_area'): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(BoulderAreaFilterType::class);
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $queryBuilder
            ->leftJoin(sprintf('%s.rock', $filterDataDto->getEntityAlias()), 'rock')
            ->andWhere('rock.boulderArea = :boulderArea')
            ->setParameter('boulderArea', $filterDataDto->getValue());
    }
}
