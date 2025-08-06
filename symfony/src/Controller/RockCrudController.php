<?php

namespace App\Controller;

use App\Entity\Rock;
use App\Field\GeoPointField;
use App\Form\ImageType;
use App\Services\CentroidCalculator;
use App\Utils\AllowContributionExpression;
use App\Utils\Roles;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rock::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rock')
            ->setEntityLabelInPlural('Rocks')
            ->setSearchFields(['boulderArea.name', 'id'])
            ->setPaginatorPageSize(10)
            ->setFormOptions(['attr' => ['novalidate' => true]])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle('detail', fn (Rock $rock) => (string) $rock)
            ->addFormTheme('form/theme.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            AssociationField::new('boulderArea', 'Boulder_area'),
            GeoPointField::new('location', 'Position')->hideOnIndex()->setTemplatePath('common/geo-point.html.twig'),
            AssociationField::new('boulders', 'Boulders')->setCssClass('cy-boulders')->hideOnForm()->setTemplatePath('common/association.html.twig')->setCustomOption('controller', BoulderCrudController::class),
            CollectionField::new('pictures')
                ->setLabel('Pictures')
                ->setFormType(CollectionType::class)
                ->setFormTypeOptions(
                    [
                        'entry_type' => ImageType::class,
                        'by_reference' => false,
                    ],
                )
                ->setTemplatePath('rocks/pictures.html.twig')
                ->addCssClass('cy-pictures'),
            DateTimeField::new('createdAt', 'Created_at')->hideOnForm(),
            AssociationField::new('createdBy', 'Created_by')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
            DateTimeField::new('updatedAt', 'Updated_at')->hideOnForm(),
            AssociationField::new('updatedBy', 'Updated_by')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, new AllowContributionExpression())
            ->setPermission(Action::EDIT, new AllowContributionExpression())
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('boulderArea', 'Boulder_area'))
            ->add(EntityFilter::new('createdBy', 'Created_by'))
            ->add(EntityFilter::new('updatedBy', 'Updated_by'))
        ;
    }

    /** @phpstan-ignore-next-line */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        CentroidCalculator::onRockUpdate($entityManager, $entityInstance);
    }

    /** @phpstan-ignore-next-line */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        CentroidCalculator::onRockUpdate($entityManager, $entityInstance);
    }

    /** @phpstan-ignore-next-line */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);
        CentroidCalculator::onRockUpdate($entityManager, $entityInstance);
    }
}
