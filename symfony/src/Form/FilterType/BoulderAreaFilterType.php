<?php

namespace App\Form\FilterType;

use App\Entity\BoulderArea;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoulderAreaFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => BoulderArea::class,
            'choice_label' => 'name',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->leftJoin('u.rocks', 'rocks')
                    ->andWhere('rocks.boulders is not empty')
                    ->orderBy('u.name', 'ASC');
            },

        ]);
    }

    public function getParent(): ?string
    {
        return EntityType::class;
    }
}
