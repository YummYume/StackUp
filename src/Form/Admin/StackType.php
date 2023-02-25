<?php

namespace App\Form\Admin;

use App\Entity\Stack;
use App\Form\ProfilesAutocompleteField;
use App\Form\TechAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StackType extends AbstractType
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
                'required' => true,
            ])
            ->add('techs', TechAutocompleteField::class, [
                'label' => 'tech.collection',
                'multiple' => true,
                'required' => true,
            ])
            ->add('profile', ProfilesAutocompleteField::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stack::class,
        ]);
    }
}
