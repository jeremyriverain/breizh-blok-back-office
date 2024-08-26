<?php

namespace App\Controller;

use App\Controller\Utils\Roles;
use App\Entity\Boulder;
use App\Entity\Media;
use App\Field\GeoPointField;
use App\Filters\Admin\BoulderAreaFilter as AdminBoulderAreaFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\Response;

class BoulderCrudController extends AbstractCrudController
{

    public function __construct(private EntityManagerInterface $em) {}

    public static function getEntityFqcn(): string
    {
        return Boulder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Boulder')
            ->setEntityLabelInPlural('Boulders')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setFormOptions(['attr' => ['novalidate' => true]]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Name'),
            BooleanField::new('isUrban')->setLabel('Urban_boulder')->renderAsSwitch(false)->setCssClass('cy-urban-boulder'),
            AssociationField::new('grade', 'Grade')->setCssClass('cy-grade'),
            TextareaField::new('description')->setTemplatePath('@EasyAdmin/crud/field/text_editor.html.twig'),
            TextField::new('rock.boulderArea', 'Boulder_area')->hideOnForm()->setTemplatePath('boulders/boulder-area.html.twig'),
            AssociationField::new('rock', 'Rock')->setFormTypeOption('group_by', 'boulderArea')->setCssClass(('cy-rocks'))->hideOnIndex(),
            GeoPointField::new('rock.location', 'Position')->hideOnForm()->hideOnIndex()->setTemplatePath('common/geo-point.html.twig'),
            AssociationField::new('lineBoulders', 'Boulder_line')->hideOnForm()->setTemplatePath('boulders/line-boulders.html.twig')->setCssClass('vue-draw-line'),
            DateTimeField::new('createdAt', 'Created_at')->hideOnForm(),
            AssociationField::new('createdBy', 'Created_by')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
        ];
    }

    private function drawLineActionFactory(): Action
    {
        return  Action::new('drawLine', 'Boulder_line')->linkToCrudAction('drawLine');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $this->drawLineActionFactory())
            ->add(Crud::PAGE_DETAIL, $this->drawLineActionFactory()->addCssClass('btn'))
            ->reorder(Crud::PAGE_DETAIL, [Action::DELETE, Action::INDEX, Action::EDIT, 'drawLine'])
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, 'drawLine', Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('rock', 'Rock'))
            ->add(EntityFilter::new('createdBy', 'Created_by'))
            ->add(BooleanFilter::new('isUrban', 'Urban_boulder'))
            ->add(AdminBoulderAreaFilter::new('boulderArea'));
    }


    public function drawLine(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        if (!$entity instanceof Boulder) {
            throw new \Exception("Instance of App\Entity\Boulder expected");
        }
        /** @var \App\Repository\MediaRepository **/
        $repository = $this->em->getRepository(Media::class);
        $rockPictures  = $entity->getRock() ? $repository->findByRockAndBoulder($entity->getRock(), $entity) : new ArrayCollection();

        return $this->render('boulders/draw-line.html.twig', [
            'rockPictures' => $rockPictures
        ]);
    }
}
