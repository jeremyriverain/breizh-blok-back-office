<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home', priority: 10)]
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('admin', ['_locale' => $request->getLocale()]);
    }

    #[Route('/admin', name: 'adminNoLocale', priority: 10)]
    public function adminNoLocale(Request $request): Response
    {
        return $this->redirectToRoute('admin', ['_locale' => $request->getLocale()]);
    }
}
