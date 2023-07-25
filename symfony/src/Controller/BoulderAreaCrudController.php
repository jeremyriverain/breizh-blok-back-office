<?php

namespace App\Controller;

use App\Controller\Utils\Roles;
use App\Entity\BoulderArea;
use App\Field\GeoPointField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class BoulderAreaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BoulderArea::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Secteur')
            ->setEntityLabelInPlural('Secteurs')
            ->setDefaultSort(['name' => 'ASC'])
            ->setFormOptions(['attr' => ['novalidate' => true]])
            ->addFormTheme('form/theme.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            AssociationField::new('municipality', 'Commune'),
            TextareaField::new('description')->setTemplatePath('@EasyAdmin/crud/field/text_editor.html.twig'),
            GeoPointField::new('parkingLocation', 'Emplacement parking')->setTemplatePath('common/geo-point.html.twig')->setRequired(false),
            ArrayField::new('rocks', 'Blocs')->setCssClass('cy-boulders')->hideOnForm()->setTemplatePath('boulder-areas/rocks.html.twig'),
            DateTimeField::new('createdAt', 'Mis à jour le')->hideOnForm(),
            AssociationField::new('createdBy', 'Créé par')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),
            AssociationField::new('updatedBy', 'Mis à jour par')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
        ];
    }

    /** @phpstan-ignore-next-line */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var BoulderArea $entityInstance
         */
        if ($entityInstance->getParkingLocation() && $entityInstance->getParkingLocation()->getLatitude() === null) {
            $parkingLocation = $entityInstance->getParkingLocation();
            $entityInstance->setParkingLocation(null);
            $entityManager->remove($parkingLocation);
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('municipality', 'Commune'))
            ->add(EntityFilter::new('createdBy', 'Créé par'))
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
