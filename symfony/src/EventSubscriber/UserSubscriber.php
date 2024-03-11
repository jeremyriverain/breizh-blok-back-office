<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class UserSubscriber
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->hashPassword($args);
    }

    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->hashPassword($args);
    }

    /**
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManager> $args
     */
    private function hashPassword(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $plainPassword = $entity->getPlainPassword();

        if (!$plainPassword) {
            return;
        }

        $entity->setPassword($this->userPasswordHasher->hashPassword($entity, $plainPassword));
    }
}
