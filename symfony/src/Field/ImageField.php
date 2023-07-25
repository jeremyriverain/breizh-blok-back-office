<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ImageField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, string|null|false $label = false): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(VichImageType::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('allow_delete', true)
            ->setFormTypeOption('download_link', true);
    }
}
