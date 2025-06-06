<?php

namespace App\EventSubscriber;

use ApiPlatform\State\Util\RequestAttributesExtractor;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Media;
use App\Interfaces\IContainsMedia;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Vich\UploaderBundle\Storage\StorageInterface;

final class ResolveIContainsMediaSubscriber implements EventSubscriberInterface
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(private StorageInterface $storage)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function onPreSerialize(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response || !$request->attributes->getBoolean('_api_respond', true)) {
            return;
        }

        if (!($attributes = RequestAttributesExtractor::extractAttributes($request)) || !class_implements($attributes['resource_class']) || !in_array(IContainsMedia::class, class_implements($attributes['resource_class']))) {
            return;
        }

        $nestedMedias = $controllerResult;

        if (!is_iterable($nestedMedias)) {
            $nestedMedias = [$nestedMedias];
        }

        foreach ($nestedMedias as $nestedMedia) {
            if (!$nestedMedia instanceof IContainsMedia) {
                continue;
            }

            $this->resolve($nestedMedia);
        }
    }

    private function resolve(IContainsMedia $nestedMedia): void
    {
        foreach ($nestedMedia->getMediaAttributes() as $mediaProperty) {

            $v = $this->propertyAccessor->getValue($nestedMedia, $mediaProperty);
            if (!empty($v)) {
                if ($v instanceof Media) {
                    $this->setContentUrl($v);
                } else if (is_array($v)) {
                    foreach ($v as $media) {
                        $this->resolve($media);
                    }
                }
            }
        }
    }

    private function setContentUrl(Media $mediaObject): void
    {
        $mediaObject->contentUrl = $this->storage->resolveUri($mediaObject, 'file');

        $mediaObject->filterUrl = '/media/cache/resolve/%filter%' . $this->storage->resolveUri($mediaObject, 'file');
    }
}
