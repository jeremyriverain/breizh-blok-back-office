<?php

namespace App\DataFixtures\Factory;

use App\Entity\Media;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Media>
 */
final class MediaFactory extends PersistentProxyObjectFactory
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public static function class(): string
    {
        return Media::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'updatedAt' => self::faker()->dateTime(),
        ];
    }

    protected function initialize(): static
    {
        return $this
          // @phpstan-ignore-next-line
          ->instantiateWith(function (array $attributes) {
              $filePath = $attributes['filePath'];
              $targetPath = $this->parameterBag->get('kernel.project_dir').'/src/DataFixtures/assets/'.$filePath;

              if (!file_exists($targetPath)) {
                  throw new FileNotFoundException('File not found');
              }

              $media = new Media();
              $tmpDir = sys_get_temp_dir();

              $absoluteTmpFile = $tmpDir.'/'.uniqid().'.'.pathinfo($targetPath, PATHINFO_EXTENSION);

              copy($targetPath, $absoluteTmpFile);

              $mimeType = mime_content_type($absoluteTmpFile);

              if (!$mimeType) {
                  throw new \Exception('mime type has not been found');
              }

              $file = new UploadedFile($absoluteTmpFile, pathinfo($absoluteTmpFile, PATHINFO_BASENAME), $mimeType, null, true);

              $media->setFile($file);

              return $media;
          })
        ;
    }
}
