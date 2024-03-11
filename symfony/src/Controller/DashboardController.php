<?php

namespace App\Controller;

use App\Controller\Utils\Roles;
use App\Entity\Boulder;
use App\Entity\BoulderArea;
use App\Entity\Department;
use App\Entity\Grade;
use App\Entity\Municipality;
use App\Entity\Rock;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{

    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    private function redirectAdmin(): Response
    {
        return $this->redirect($this->adminUrlGenerator->setController(BoulderAreaCrudController::class)->generateUrl());
    }

    #[Route('/', name: 'homepage', priority: 10)]
    public function homepage(): Response
    {
        return $this->redirectAdmin();
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectAdmin();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Topos');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Départements', 'fas fa-users', Department::class)->setPermission(Roles::SUPER_ADMIN->value);
        yield MenuItem::linkToCrud('Communes', 'fab fa-unity', Municipality::class);
        yield MenuItem::linkToCrud('Secteurs', 'fas fa-cubes', BoulderArea::class);
        yield MenuItem::linkToCrud('Rochers', 'fas fa-cube', Rock::class);
        yield MenuItem::linkToCrud('Blocs', 'fas fa-level-up-alt', Boulder::class);

        yield MenuItem::section('Configuration');

        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class)->setPermission(Roles::SUPER_ADMIN->value);
        yield MenuItem::linkToCrud('Cotations', 'fas fa-ruler-vertical', Grade::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if (!$user instanceof User) {
            throw new \Exception("Instance of App\Entity\User expected");
        }

        $url = $url = $this->adminUrlGenerator
            ->setController(UserCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($user->getId())
            ->generateUrl();
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToUrl('Mon profil', 'fa fa-id-card', $url),
            ]);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(30);
    }
}
