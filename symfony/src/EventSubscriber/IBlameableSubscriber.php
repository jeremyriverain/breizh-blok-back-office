<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Interfaces\IBlameable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
class IBlameableSubscriber
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof IBlameable) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $entity->setCreatedBy($user);
        }
    }
}
