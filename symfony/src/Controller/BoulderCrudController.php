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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\Response;

class BoulderCrudController extends AbstractCrudController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Boulder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bloc')
            ->setEntityLabelInPlural('Blocs')
            ->setDefaultSort(['name' => 'ASC'])
            ->setFormOptions(['attr' => ['novalidate' => true]]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            AssociationField::new('grade', 'Cotation')->setCssClass('cy-grade'),
            TextareaField::new('description')->setTemplatePath('@EasyAdmin/crud/field/text_editor.html.twig'),
            TextField::new('rock.boulderArea', 'Secteur')->hideOnForm()->setTemplatePath('boulders/boulder-area.html.twig'),
            AssociationField::new('rock', 'Rocher')->setFormTypeOption('group_by', 'boulderArea')->setCssClass(('cy-rocks'))->hideOnIndex(),
            GeoPointField::new('rock.location', 'Position')->hideOnForm()->hideOnIndex()->setTemplatePath('common/geo-point.html.twig'),
            AssociationField::new('lineBoulders', 'Ligne du bloc')->hideOnForm()->setTemplatePath('boulders/line-boulders.html.twig')->setCssClass('vue-draw-line'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            AssociationField::new('createdBy', 'Créé par')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),
            AssociationField::new('updatedBy', 'Mis à jour par')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
        ];
    }

    private function drawLineActionFactory(): Action
    {
        return  Action::new('drawLine', 'Ligne du bloc')->linkToCrudAction('drawLine');
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
            ->add(EntityFilter::new('rock', 'Rocher'))
            ->add(EntityFilter::new('createdBy', 'Créé par'))
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
