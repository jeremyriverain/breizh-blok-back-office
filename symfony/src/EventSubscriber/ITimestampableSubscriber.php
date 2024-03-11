<?php

namespace App\EventSubscriber;

use App\Interfaces\ITimestampable;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
class ITimestampableSubscriber
{
    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ITimestampable) {
            return;
        }
        $entity->setCreatedAt(Carbon::now()->toImmutable());
    }
}
