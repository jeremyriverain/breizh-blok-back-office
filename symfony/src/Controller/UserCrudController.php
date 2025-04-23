<?php

namespace App\Controller;

use App\Utils\Roles;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setFormOptions(['validation_groups' => ['Default', 'registration'], 'attr' => ['novalidate' => true]], ['validation_groups' => ['Default'], 'attr' => ['novalidate' => true]])
            ->setPageTitle('detail', fn(User $user) => (string) $user)
            ->setDefaultSort(['email' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            ChoiceField::new('roles')->allowMultipleChoices(true)->setChoices([
                Roles::USER->value => Roles::USER->value,
                Roles::CONTRIBUTOR->value => Roles::CONTRIBUTOR->value,
                Roles::ADMIN->value => Roles::ADMIN->value,
                Roles::SUPER_ADMIN->value => Roles::SUPER_ADMIN->value,
            ])->renderExpanded(true)->renderAsBadges([
                Roles::CONTRIBUTOR->value => 'primary',
                Roles::ADMIN->value => 'warning',
                Roles::SUPER_ADMIN->value => 'danger',
            ]),
            DateTimeField::new('lastAuthenticatedAt', 'Last_connection')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->setPermission(Action::NEW, Roles::SUPER_ADMIN->value)
            ->setPermission(Action::DETAIL, Roles::SUPER_ADMIN->value)
            ->setPermission(Action::DELETE, Roles::SUPER_ADMIN->value);
    }
}
