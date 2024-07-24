<?php

namespace App\EventSubscriber;

use App\Entity\Media;
use Vich\UploaderBundle\Event\Event;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Vich\UploaderBundle\Storage\StorageInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class MediaSubscriber
{

    public function __construct(private StorageInterface $storage, private CacheManager $cacheManager)
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

        $this->cacheManager->remove($this->storage->resolvePath($object, 'file', null, true));
    }
}
