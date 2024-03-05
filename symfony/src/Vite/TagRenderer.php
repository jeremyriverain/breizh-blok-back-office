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
        $filePath = '/build/' . $entryNameData['css'][0];
        return '<link rel="stylesheet" href="' . $filePath . '">';
    }
    public function renderScriptTag(string $entryName): string
    {
        $entryNameData = $this->manifestLookup->getManifestContent()[$entryName . ".ts"];
        $filePath = '/build/' . $entryNameData['file'];
        return  '<script type="module" crossorigin src="' . $filePath . '"></script>';
    }
}
