<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use Gedmo\Blameable\BlameableListener;
use Symfony\Bundle\SecurityBundle\Security;

class BlameSubscriber implements EventSubscriberInterface
{
    public function __construct(private BlameableListener $blameableListener, private Security $security)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        if ($this->security->getUser()) {
            $this->blameableListener->setUserValue($this->security->getUser());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
