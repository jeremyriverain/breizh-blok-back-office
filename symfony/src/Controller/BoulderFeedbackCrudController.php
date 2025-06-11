<?php

namespace App\Controller;

use App\Entity\BoulderFeedback;
use App\Field\GeoPointField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BoulderFeedbackCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BoulderFeedback::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('BoulderFeedback')
            ->setEntityLabelInPlural('BoulderFeedbacks')
            ->setDefaultSort(['receivedAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('receivedAt', 'receivedAt'),
            TextField::new('sentBy', 'sentBy'),
            AssociationField::new('boulder'),
            TextareaField::new('message', 'message')->hideOnIndex(),
            BooleanField::new('message', 'message')->hideOnDetail(),
            GeoPointField::new('newLocation', 'newLocation')->setTemplatePath('common/geo-point.html.twig')->setRequired(false),
            AssociationField::new('newGrade', 'newGrade'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::BATCH_DELETE)
            ;
    }
}
