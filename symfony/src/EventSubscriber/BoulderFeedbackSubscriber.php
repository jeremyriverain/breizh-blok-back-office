<?php

namespace App\EventSubscriber;

use App\Controller\BoulderFeedbackCrudController;
use App\Entity\BoulderFeedback;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::postPersist)]
class BoulderFeedbackSubscriber
{
    public function __construct(
        private Security $security,
        private MailerInterface $mailer,
        private TranslatorInterface $translator,
        private AdminUrlGenerator $adminUrlGenerator,
        private string $developerEmail,
    ) {}

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
        if ($user === null) {
            return;
        }
        
        $entity->setSentBy($user->getUserIdentifier());
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof BoulderFeedback) {
            return;
        }
       
        $url = $this->adminUrlGenerator
                ->setAction(Action::DETAIL)
                ->setEntityId($entity->getId())
                ->setController(BoulderFeedbackCrudController::class)
                ->set('_locale', $this->translator->getLocale())
                ->generateUrl();

        $email = NotificationEmail::asPublicEmail()
            ->from($_ENV['MAILER_RECIPIENT'])
            ->to($this->developerEmail)
            ->subject($this->translator->trans('boulderFeedback.email.subject', []))
            ->content($this->translator->trans('boulderFeedback.email.content', []))
            ->action($this->translator->trans('boulderFeedback.email.action', []), $url);

        $this->mailer->send($email);
    }
}
