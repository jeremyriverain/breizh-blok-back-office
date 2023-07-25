<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaFixtures extends Fixture
{
    public const BOULDER_IMG = 'boulder_img';

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $rock_image1 = $this->makeMedia(('boulder1.jpg'));
        $manager->persist($rock_image1);

        $manager->flush();

        $this->addReference(self::BOULDER_IMG, $rock_image1);
    }

    public function makeMedia(string $filePath): Media
    {
        $targetPath = $this->parameterBag->get('kernel.project_dir') . "/src/DataFixtures/images/" . $filePath;

        if (!file_exists($targetPath)) {
            throw new FileNotFoundException("File not found");
        }

        $media = new Media();

        $tmpDir = sys_get_temp_dir();

        $absoluteTmpFile = $tmpDir . "/" . uniqid() . "." . pathinfo($targetPath, PATHINFO_EXTENSION);

        copy($targetPath, $absoluteTmpFile);

        $mimeType = mime_content_type($absoluteTmpFile);

        if (!$mimeType) {
            throw new \Exception("mime type has not been found");
        }

        $file = new UploadedFile($absoluteTmpFile, pathinfo($absoluteTmpFile, PATHINFO_BASENAME), $mimeType, null, true);

        $media->setFile($file);
        return $media;
    }
}
