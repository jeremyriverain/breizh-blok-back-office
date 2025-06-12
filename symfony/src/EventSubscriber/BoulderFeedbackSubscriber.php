<?php

namespace App\EventSubscriber;

use App\Entity\BoulderFeedback;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
class BoulderFeedbackSubscriber
{
    public function __construct(private Security $security) {}

    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof BoulderFeedback) {
            return;
        }

        $user = $this->security->getUser();
        if ($user !== null) {
            $entity->setSentBy($user->getUserIdentifier());
        }

        $entity->setReceivedAt(
            new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
        );
    }
}
