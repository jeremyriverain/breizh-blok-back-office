<?php

namespace App\Vite;

class TagRenderer
{
    public function __construct(private ManifestLookup $manifestLookup)
    {
    }
    public function renderLinkTag(string $entryName): string
    {
        $entryNameData = $this->manifestLookup->getManifestContent()[$entryName . ".ts"];
        $cssFile = $entryNameData['css'][0];
        if (!$cssFile) {
            throw new \Exception("css file $entryName is not referenced in the manifest", 1);
        }
        $filePath = '/build/' . $cssFile;
        return '<link rel="stylesheet" href="' . $filePath . '">';
    }
    public function renderScriptTag(string $entryName): string
    {
        $entryNameData = $this->manifestLookup->getManifestContent()[$entryName . ".ts"];
        $filePath = '/build/' . $entryNameData['file'];
        return  '<script type="module" crossorigin src="' . $filePath . '"></script>';
    }
}
