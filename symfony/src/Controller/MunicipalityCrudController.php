<?php

namespace App\Controller;

use App\Controller\Utils\Roles;
use App\Entity\Municipality;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class MunicipalityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Municipality::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commune')
            ->setEntityLabelInPlural('Communes')
            ->setDefaultSort(['name' => 'ASC'])
            ->setFormOptions(['attr' => ['novalidate' => true]]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            TextField::new('code', 'Code')->setHelp('voir https://api.gouv.fr/documentation/api-geo pour récupérer un code valide')->setPermission(Roles::SUPER_ADMIN->value),
            AssociationField::new('department', 'Département'),
            AssociationField::new('boulderAreas', 'Secteurs')->hideOnForm()->setTemplatePath('municipality/boulder-areas.html.twig')->setCustomOption('controller', BoulderAreaCrudController::class)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->setPermission(Action::NEW, Roles::SUPER_ADMIN->value)
            ->setPermission(Action::DELETE, Roles::SUPER_ADMIN->value)
            ->setPermission(Action::EDIT, Roles::SUPER_ADMIN->value)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('department', 'Département'));
    }
}
