<?php

namespace App\Twig;

use App\Entity\Boulder;
use App\Entity\LineBoulder;
use App\Entity\Media;
use App\Vite\TagRenderer;
use Doctrine\Common\Collections\Collection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vich\UploaderBundle\Storage\StorageInterface;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private CacheManager $cacheManager,
        private StorageInterface $storage,
        private TagRenderer $tagRenderer,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('my_imagine_filter', [$this, 'myImagineFilter']),
            new TwigFilter('line_boulder', [$this, 'lineBoulderFilter']),
            new TwigFilter('decode_base_64', [$this, 'decodeBase64']),
        ];
    }

    public function myImagineFilter(string $path, string $filter): string
    {
        if (!$path) {
            return '';
        }
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ('svg' === $ext) {
            return '/'.$path;
        } else {
            return $this->cacheManager->getBrowserPath($path, $filter, [], null);
        }
    }

    /**
     * @param Collection<int, LineBoulder>|LineBoulder[] $lineBoulders
     */
    public function lineBoulderFilter(Collection $lineBoulders, Boulder $boulder): ?LineBoulder
    {
        $lineBoulders = $lineBoulders->filter(function ($lineBoulder) use ($boulder) {
            /* @var LineBoulder $lineBoulder */
            return $boulder === $lineBoulder->getBoulder();
        });

        return $lineBoulders->first() ? $lineBoulders->first() : null;
    }

    public function decodeBase64(?Media $media): ?string
    {
        if (!$media) {
            return null;
        }

        $imageAbsolutePath = $this->storage->resolvePath($media, 'file', null, false);

        if (!$imageAbsolutePath) {
            return null;
        }

        try {
            $image = file_get_contents($imageAbsolutePath);

            if (false === $image) {
                return null;
            }

            return 'data:image/png;base64,'.base64_encode($image);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('vite_style_tag', [$this, 'renderViteStyleTag'], ['is_safe' => ['html']]),
            new TwigFunction('vite_script_tag', [$this, 'renderViteScriptTag'], ['is_safe' => ['html']]),
        ];
    }

    public function renderViteStyleTag(string $entryName): string
    {
        return $this->tagRenderer->renderLinkTag($entryName);
    }

    public function renderViteScriptTag(string $entryName): string
    {
        return $this->tagRenderer->renderScriptTag($entryName);
    }
}
