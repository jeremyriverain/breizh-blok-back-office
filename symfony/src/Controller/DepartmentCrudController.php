<?php

namespace App\Controller;

use App\Controller\Utils\Roles;
use App\Entity\Department;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DepartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Department::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Département')
            ->setEntityLabelInPlural('Départements')
            ->setDefaultSort(['name' => 'ASC'])
            ->setFormOptions(['attr' => ['novalidate' => true]]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            AssociationField::new('municipalities', 'Communes')->hideOnForm()
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
}
