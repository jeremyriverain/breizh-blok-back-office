<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class AuthenticationSubscriber implements EventSubscriberInterface
{

    public function __construct(private Security $security, private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                'onInteractiveLogin'
            ]
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $this->security->getUser();
        if (!$user || !$user instanceof User) {
            throw new \Exception("User should be there");
        }
        $user->setLastAuthenticatedAt(Carbon::now());
        $this->em->flush();
    }
}
