<?php

namespace App\EventSubscriber;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use App\Entity\Boulder;
use App\Entity\Municipality;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ResolveMunicipalityItemGetSubscriber implements EventSubscriberInterface
{

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

        $attributes = RequestAttributesExtractor::extractAttributes($request);

        if (!($attributes = RequestAttributesExtractor::extractAttributes($request)) || !\is_a($attributes['resource_class'], Municipality::class, true)) {
            return;
        }

        if (!$attributes['operation'] instanceof Get) {
            return;
        }

        /**
         * @var Municipality $municipality
         */
        $municipality = $controllerResult;

        foreach ($municipality->getBoulderAreas() as $boulderArea) {
            $bouldersSortedByGrade = $boulderArea->getBouldersSortedByGrade();
            $numberOfBoulders = count($bouldersSortedByGrade);
            $boulderArea->numberOfBoulders = $numberOfBoulders;
            if ($numberOfBoulders > 0) {
                $boulderArea->lowestGrade = $bouldersSortedByGrade[0]->getGrade();
                $bouldersWithoutNullGrade = array_filter($bouldersSortedByGrade,  fn (Boulder $value) => $value->getGrade() !== null);
                $potentialHighestGrade = count($bouldersWithoutNullGrade) === 0 ? null : $bouldersWithoutNullGrade[count($bouldersWithoutNullGrade) - 1];
                $boulderArea->highestGrade = $potentialHighestGrade ? $potentialHighestGrade->getGrade() : null;
            }
        }
    }
}
