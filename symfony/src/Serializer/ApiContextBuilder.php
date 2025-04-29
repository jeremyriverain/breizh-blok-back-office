<?php

namespace App\Serializer;

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

        /**
         * @phpstan-ignore-next-line
         */
        $context['groups'] = array_merge($this->getCommonContextGroups($className, $normalization), $context['groups']);

        return $context;
    }

    /**
     * @return array<string>
     */
    private function getCommonContextGroups(string $className, bool $normalization): array
    {
        if (!$normalization) {
            return ["$className:write", "write"];
        }
        return ["$className:read", "read"];
    }
}
