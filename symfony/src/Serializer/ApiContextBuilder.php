<?php
// api/src/Serializer/TblClientContextBuilder.php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;

final class ApiContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(private SerializerContextBuilderInterface $decorated)
    {
    }

    /**
     * @return array<string>
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

        $operationName = array_key_exists('item_operation_name', $context) ? $context['item_operation_name'] : $context['collection_operation_name'];
        $operationType = $context['operation_type'] . "-" . $operationName;

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
