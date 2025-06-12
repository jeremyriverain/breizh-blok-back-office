<?php

namespace App\EventSubscriber;

use App\Interfaces\IUpdatable;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::preUpdate)]
class IUpdatableSubscriber
{
    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof IUpdatable) {
            return;
        }
        $entity->setUpdatedAt(Carbon::now()->toImmutable());
    }
}
