<?php

namespace App\Form;

use App\Entity\Tech;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TechType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'tech.name',
                'required' => true,
                'help' => 'tech.name.help',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'tech.description',
                'required' => false,
                'help' => 'tech.description.help',
            ])
            ->add('links', TechLinksType::class, [
                'label' => false,
            ])
            ->add('categories', CategoriesAutocompleteField::class, [
                'label' => 'tech.categories',
            ])
            ->add('dependsOn', TechAutocompleteField::class, [
                'label' => 'tech.depends_on',
                'help' => 'tech.depends_on.help',
            ])
            ->add('picture', TechPictureType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tech::class,
        ]);
    }
}
