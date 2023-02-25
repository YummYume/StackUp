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

final class TechType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'common.form.name',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'common.form.description',
                'required' => false,
            ])
            ->add('type', EnumType::class, [
                'label' => 'common.form.type',
                'class' => TechTypeEnum::class,
                'autocomplete' => true,
                'required' => true,
            ])
            ->add('links', TechLinksType::class, [
                'label' => false,
            ])
            ->add('categories', CategoriesAutocompleteField::class, [
                'label' => 'category.collection',
            ])
            ->add('dependsOn', TechAutocompleteField::class, [
                'label' => 'common.form.depends_on',
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
