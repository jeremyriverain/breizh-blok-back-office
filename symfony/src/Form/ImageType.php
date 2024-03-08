<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Storage\StorageInterface;

class ImageType extends AbstractType
{
    public function __construct(private StorageInterface $storage)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /**
             * @var Media | null
             */
            $media = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$media || null === $media->getId()) {
                $form
                    ->add('file', VichImageType::class, [
                        'label' => false,
                        'required' => true,
                        'allow_delete' => false, // not mandatory, default is true
                        'download_uri' => true, // not mandatory, default is true
                        'constraints' => [
                            new Assert\Image(),
                            new Assert\File(mimeTypes: ['image/jpeg', 'image/png', 'image/webp'], maxSize: '8M'),
                            new Assert\NotNull(),
                        ],
                        'attr' => [
                            'class' => 'cy-picture-input'
                        ]
                    ]);
            } else {
                $form->add('hidden_vich', HiddenType::class, [
                    'mapped' => false,
                    'attr' => [
                        'image' => $this->storage->resolvePath($media, 'file', null, true),
                        'class' => 'js-hidden-vich'
                    ]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
