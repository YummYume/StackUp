<?php

namespace App\Form;

use App\Entity\TechPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class TechPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', VichImageType::class, [
                'label' => 'tech_picture.file',
                'help' => 'tech_picture.file.help',
                'required' => false,
                'allow_delete' => true,
                'image_alt' => 'tech_picture.current_picture',
                'image_imagine_filter' => 'tech_picture',
                'image_class' => 'rounded-3xl',
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TechPicture::class,
        ]);
    }
}
