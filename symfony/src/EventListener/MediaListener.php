<?php

namespace App\EventListener;

use App\Entity\Media;
use Vich\UploaderBundle\Event\Event;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Vich\UploaderBundle\Storage\StorageInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaListener
{

    public function __construct(private StorageInterface $storage, private CacheManager $cacheManager, private ParameterBagInterface $parameterBag)
    {
    }


    public function onVichUploaderPreUpload(Event $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof Media) {
            return;
        }

        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($object->getFile()?->getPathName() ?? '');
    }

    public function onVichUploaderPreRemove(Event $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof Media) {
            return;
        }

        // try {
        // in case the asset does not exist
        $path = $this->parameterBag->get('general_images_relative_path') . "/" . $this->storage->resolvePath($object, 'file', null, true);
        $this->cacheManager->remove($path);
        // } catch (\Throwable $error) {
        // }
    }
}
