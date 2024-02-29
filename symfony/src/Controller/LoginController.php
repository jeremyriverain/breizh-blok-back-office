<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class LoginController extends AbstractController
{
    #[Route('/admin/login', name: 'login')]
    public function requestLoginLink(MailerInterface $mailer, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user || !$user->getEmail()) {
                $this->addFlash(
                    'info',
                    'L\'email n\'a pas été trouvé'
                );
                return $this->redirectToRoute('login');
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            $this->addFlash(
                'info',
                "Un email d'authentification vous a été envoyé"
            );

            $email = NotificationEmail::asPublicEmail()
                ->from('riverainjeremy@gmail.com')
                ->to($user->getEmail())
                ->subject('Se connecter à Breizh Blok')
                ->content("Cliquez sur le bouton ci-dessous pour vous authentifier. Ce lien n'est valide que 10 minutes.")
                ->action('Se connecter', $loginLinkDetails->getUrl());

            $mailer->send($email);

            return $this->redirectToRoute('login');
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/login.html.twig');
    }

    #[Route('/admin/logout', name: 'app_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/admin/login_check', name: 'login_check')]
    public function check(): never
    {
        throw new \LogicException('This code should never be reached');
    }
}
