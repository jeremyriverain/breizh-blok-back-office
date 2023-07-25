<?php

namespace App\Filters\Api;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class BoulderTermFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        // otherwise filter is applied to order and page as well
        if (!$this->isPropertyEnabled($property, $resourceClass) || !is_string($value)) {
            return;
        }

        if ($property === 'term') {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $rockAlias = $queryNameGenerator->generateJoinAlias('rock');
            $boulderAreaAlias = $queryNameGenerator->generateJoinAlias('boulderArea');
            $municipalityAlias = $queryNameGenerator->generateJoinAlias('municipality');
            $parameterName = $queryNameGenerator->generateParameterName($property); // Generate a unique parameter name to avoid collisions with other filters

            $queryBuilder
                ->leftJoin("$rootAlias.rock", $rockAlias)
                ->leftJoin("$rockAlias.boulderArea", $boulderAreaAlias)
                ->leftJoin("$boulderAreaAlias.municipality", $municipalityAlias)
                ->andWhere(
                    sprintf(
                        $rootAlias . '.name like :%1$s or ' . $boulderAreaAlias . '.name like :%2$s or ' . $municipalityAlias . '.name like :%2$s',
                        "partial_$parameterName",
                        "start_$parameterName"
                    )
                )
                ->setParameter("partial_$parameterName", "%$value%")
                ->setParameter("start_$parameterName", "$value%");
        }
    }

    // @phpstan-ignore-next-line
    public function getDescription(string $resourceClass): array
    {
        $description = [];
        $description["term"] = [
            'property' => "term",
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'swagger' => [
                'description' => 'Search for boulders by name, boulder area',
                'name' => 'term',
                'type' => 'Search for boulders by name, boulder area',
            ],
        ];

        return $description;
    }
}
