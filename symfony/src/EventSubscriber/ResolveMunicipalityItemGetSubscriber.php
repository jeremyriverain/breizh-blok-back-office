<?php
namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
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

        if (!array_key_exists('item_operation_name', $attributes) || $attributes['item_operation_name'] !== 'get') {
            return;
        }

        /**
         * @var Municipality $municipality
         */
        $municipality = $controllerResult;

        foreach ($municipality->getBoulderAreas() as $boulderArea) {
            $bouldersSortedByGrade = $boulderArea->getBouldersSortedByGrade();
            $numberOfBoulders = count($bouldersSortedByGrade);
            $boulderArea->setNumberOfBoulders($numberOfBoulders);
            if ($numberOfBoulders > 0) {
                $boulderArea->setLowestGrade($bouldersSortedByGrade[0]->getGrade());
                $bouldersWithoutNullGrade = array_filter($bouldersSortedByGrade,  fn(Boulder $value) => $value->getGrade() !== null);
                $potentialHighestGrade = count($bouldersWithoutNullGrade) === 0 ? null : $bouldersWithoutNullGrade[count($bouldersWithoutNullGrade) - 1];
                $boulderArea->setHighestGrade($potentialHighestGrade ? $potentialHighestGrade->getGrade() : null);
            }
        }

    }
}