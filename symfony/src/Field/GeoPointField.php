<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use App\Form\GeoPointType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;

final class GeoPointField implements FieldInterface
{

    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(GeoPointType::class)
            ->addCssClass('geo-point-field');
    }
}
