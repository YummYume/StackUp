<?php

namespace App\Form\Admin;

use App\Entity\Tech;
use App\Enum\TechTypeEnum;
use App\Form\CategoriesAutocompleteField;
use App\Form\TechAutocompleteField;
use App\Form\TechLinksType;
use App\Form\TechPictureType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechType extends AbstractType
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
            ->add('type', EnumType::class, [
                'label' => 'tech.type',
                'class' => TechTypeEnum::class,
                'autocomplete' => true,
                'required' => true,
                'help' => 'tech.type.help',
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
            ->add('picture', TechPictureType::class, [
                'label' => false,
            ])
            ->add('request', RequestType::class, [
                'label' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tech::class,
        ]);
    }
}
