<?php

namespace App\Controller;

use App\Utils\AllowContributionExpression;
use App\Utils\Roles;
use App\Entity\Boulder;
use App\Entity\HeightBoulder;
use App\Entity\Media;
use App\Field\GeoPointField;
use App\Filters\Admin\BoulderAreaFilter as AdminBoulderAreaFilter;
use App\Repository\HeightBoulderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class BoulderCrudController extends AbstractCrudController
{

    public function __construct(private EntityManagerInterface $em, private TranslatorInterface $translator) {}

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
            ->setPaginatorPageSize(10)
            ->setPageTitle('detail', fn(Boulder $boulder) => (string) $boulder)
            ->setFormOptions(['attr' => ['novalidate' => true]]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Name'),
            BooleanField::new('isUrban')->setLabel('Urban_boulder')->renderAsSwitch(false)->setCssClass('cy-urban-boulder'),
            AssociationField::new('grade', 'Grade')->setCssClass('cy-grade'),
            AssociationField::new('height', 'Height')->hideOnIndex()->setCssClass('cy-height')->setFormTypeOptions([
                'query_builder' => function (HeightBoulderRepository $repository) {
                    return self::orderHeightBoulders($repository);
                },
                'choice_label' => function (HeightBoulder $height) {
                    return HeightBoulder::trans(
                        translator: $this->translator,
                        heightBoulder: $height
                    );
                },
            ])->setTemplatePath('boulders/height.html.twig'),
            TextareaField::new('description')->setTemplatePath('@EasyAdmin/crud/field/text_editor.html.twig'),
            TextField::new('rock.boulderArea', 'Boulder_area')->hideOnForm()->setTemplatePath('boulders/boulder-area.html.twig'),
            AssociationField::new('rock', 'Rock')->setCssClass(('cy-rocks'))->hideOnIndex()
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->addCriteria(Criteria::create()
                        ->orderBy(['id' => Order::Descending]))
                ),
            GeoPointField::new('rock.location', 'Position')->hideOnForm()->hideOnIndex()->setTemplatePath('common/geo-point.html.twig'),
            AssociationField::new('lineBoulders', 'Boulder_line')->hideOnForm()->setTemplatePath('boulders/line-boulders.html.twig')->setCssClass('vue-draw-line'),
            DateTimeField::new('createdAt', 'Created_at')->hideOnForm(),
            AssociationField::new('createdBy', 'Created_by')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
            DateTimeField::new('updatedAt', 'Updated_at')->hideOnForm()->setCssClass('cy_updated_at'),
            AssociationField::new('updatedBy', 'Updated_by')->setCssClass('cy_updated_by')->setPermission(Roles::SUPER_ADMIN->value)->hideOnForm(),
        ];
    }

    private static function drawLineActionFactory(): Action
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
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, 'drawLine', Action::DELETE])
            ->setPermission(Action::DELETE, new AllowContributionExpression())
            ->setPermission(Action::EDIT, new AllowContributionExpression())
            ->setPermission('drawLine', new AllowContributionExpression())
        ;
    }

    private static function orderHeightBoulders(HeightBoulderRepository $repository): QueryBuilder
    {
        return $repository->createQueryBuilder('c')->orderBy('c.min', 'ASC');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('rock', 'Rock'))
            ->add(EntityFilter::new('createdBy', 'Created_by'))
            ->add(EntityFilter::new('updatedBy', 'Updated_by'))
            ->add(BooleanFilter::new('isUrban', 'Urban_boulder'))
            ->add(
                EntityFilter::new('height', 'Height')
                    ->setFormTypeOption(
                        'value_type_options.query_builder',
                        static fn(HeightBoulderRepository $repository) => self::orderHeightBoulders($repository)
                    )
                    ->setFormTypeOption(
                        'value_type_options.choice_label',
                        function (HeightBoulder $height) {
                            return HeightBoulder::trans(
                                translator: $this->translator,
                                heightBoulder: $height
                            );
                        },
                    )
            )
            ->add(AdminBoulderAreaFilter::new('boulderArea'));
    }


    #[AdminAction(routePath: '{entityId}/draw-line', routeName: 'draw_line', methods: ['GET'])]
    public function drawLine(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        if (!$entity instanceof Boulder) {
            throw new \Exception("Instance of App\Entity\Boulder expected");
        }

        if (!$this->isGranted(Roles::ADMIN->value) && $entity->getCreatedBy() !== $context->getUser()) {
            throw new AccessDeniedException();
        }
        /** @var \App\Repository\MediaRepository **/
        $repository = $this->em->getRepository(Media::class);
        $rockPictures  = $entity->getRock() ? $repository->findByRockAndBoulder($entity->getRock(), $entity) : new ArrayCollection();

        return $this->render('boulders/draw-line.html.twig', [
            'rockPictures' => $rockPictures
        ]);
    }
}
