<?php

namespace App\Vite;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ManifestLookup
{
    /**
     * @var array<string, array{file: string, src: string}> $manifestContent
     */
    private array $manifestContent;
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $filePath = $parameterBag->get('kernel.project_dir') . '/public/build/.vite/manifest.json';
        if (!file_exists($filePath)) {
            return;
        }
        $manifestFile = file_get_contents($filePath);
        if ($manifestFile) {
            $manifestContent = json_decode($manifestFile, true) ?? [];
            if (!is_array($manifestContent)) {
                throw new \Exception('manifest file should be an array');
            }
            $this->manifestContent = $manifestContent;
        }
    }

    /**
     * @return array<string, array{file: string, src: string}>
     */
    public function getManifestContent(): array
    {
        return $this->manifestContent;
    }
}
