<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    #[Route('/admin/login/{_locale<en|fr>}', name: 'login')]
    public function requestLoginLink(MailerInterface $mailer, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request, TranslatorInterface $translator): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user || !$user->getEmail()) {
                $this->addFlash('info', $translator->trans('submit_message', [], 'login'));
                return $this->redirectToRoute('login');
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            $this->addFlash('info', $translator->trans('submit_message', [], 'login'));

            $email = NotificationEmail::asPublicEmail()
                ->from($_ENV['MAILER_RECIPIENT'])
                ->to($user->getEmail())
                ->subject($translator->trans('email.subject', [], 'login'))
                ->content($translator->trans('email.content', [], 'login'))
                ->action($translator->trans('email.action', [], 'login'), $loginLinkDetails->getUrl());

            $mailer->send($email);

            return $this->redirectToRoute('login');
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/login.html.twig');
    }

    #[Route('/admin/login', name: 'loginNoLocale')]
    public function loginNoLocale(Request $request): Response
    {
        return $this->redirectToRoute('login', ['_locale' => $request->getPreferredLanguage(['fr', 'en'])]);
    }


    #[Route('/admin/logout/{_locale<en|fr>}', name: 'app_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/admin/login_check/{_locale<en|fr>}', name: 'login_check')]
    public function check(): never
    {
        throw new \LogicException('This code should never be reached');
    }
}
