<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Tech;
use App\Enum\TechTypeEnum;
use App\Form\TechPictureType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'label' => 'common.form.name',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'common.form.description',
                'required' => true,
            ])
            ->add('type', EnumType::class, [
                'label' => 'common.form.type',
                'class' => TechTypeEnum::class,
                'autocomplete' => true,
                'required' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'label' => 'category.collection',
                'choice_label' => 'name',
                'translation_domain' => 'tables',
                'multiple' => true,
                'autocomplete' => true,
                'required' => true,
            ])
            ->add('picture', TechPictureType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('links', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
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
