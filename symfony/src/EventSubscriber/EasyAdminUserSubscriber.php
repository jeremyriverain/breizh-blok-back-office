<?php

namespace App\EventSubscriber;

use App\Controller\Utils\Roles;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EasyAdminUserSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityUpdatedEvent::class => ['checkPermissions'],
        ];
    }

    public function checkPermissions(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $user = $this->security->getUser();

        if ($this->authorizationChecker->isGranted(Roles::SUPER_ADMIN->value)) {
            return;
        }

        if (!$user || !$user instanceof User || $user->getId() !== $entity->getId()) {
            throw new AccessDeniedException("You cannot edit an other profile than yours");
        }
    }
}
