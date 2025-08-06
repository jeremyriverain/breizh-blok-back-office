<?php

namespace App\Services;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaBase64Converter
{
    public function toMedia(string $dataUri): Media
    {
        $mimeType = 'image/png';
        $encodedImage = explode(',', $dataUri)[1];
        $decodedImage = base64_decode($encodedImage);

        $media = new Media();

        $tmpDir = sys_get_temp_dir();

        $absoluteTmpFile = $tmpDir.'/'.uniqid().'.'.explode('/', $mimeType)[1];

        file_put_contents($absoluteTmpFile, $decodedImage);

        $file = new UploadedFile($absoluteTmpFile, pathinfo($absoluteTmpFile, PATHINFO_BASENAME), $mimeType, null, true);

        $media->setFile($file);

        return $media;
    }
}
