<?php
// api/src/Serializer/TblClientContextBuilder.php

namespace App\Serializer;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

final class ApiContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(private SerializerContextBuilderInterface $decorated) {}

    /**
     * @phpstan-ignore-next-line
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        $resourceClass = $context['resource_class'] ?? null;

        if (!$resourceClass) {
            return $context;
        }

        $path = explode('\\', $resourceClass);
        $className = array_pop($path);

        if (!isset($context['groups'])) {
            $context['groups'] = [];
        }

        $operationType = '';

        /**
         * @phpstan-ignore-next-line
         */
        switch (get_class($context['operation'])) {
            case GetCollection::class:
                $operationType = 'collection-get';
                break;
            case Get::class:
                $operationType = 'item-get';
                break;
            case Post::class:
                $operationType = 'collection-post';
                break;
            case Put::class:
                $operationType = 'item-put';
                break;
            case Delete::class:
                $operationType = 'item-delete';
                break;
            case Patch::class:
                $operationType = 'item-patch';
                break;
            default:
                throw new \Exception("opreration not implemented");
        }

        /**
         * @phpstan-ignore-next-line
         */
        $context['groups'] = array_merge($this->getCommonContextGroups($className, $normalization, $operationType), $context['groups']);

        return $context;
    }

    /**
     * @return array<string>
     */
    private function getCommonContextGroups(string $className, bool $normalization, string $operationType): array
    {
        if (!$normalization) {
            return ["$className:write", "$className:$operationType", "write", "$operationType"];
        }
        return ["$className:read", "$className:$operationType", "read", "$operationType"];
    }
}
