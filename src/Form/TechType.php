<?php

namespace App\Form;

use App\Entity\Tech;
use App\Enum\TechTypeEnum;
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
                'help' => 'tech.categories.help',
            ])
            ->add('dependsOn', TechAutocompleteField::class, [
                'label' => 'tech.depends_on',
                'help' => 'tech.depends_on.help',
            ])
            ->add('picture', TechPictureType::class, [
                'label' => false,
            ])
        ;

        if ($options['can_edit_type']) {
            $builder->add('type', EnumType::class, [
                'label' => 'common.form.type',
                'class' => TechTypeEnum::class,
                'autocomplete' => true,
                'required' => true,
                'choice_label' => static fn (TechTypeEnum $type): string => 'tech.type.'.$type->value,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tech::class,
            'can_edit_type' => false,
        ]);
    }
}
