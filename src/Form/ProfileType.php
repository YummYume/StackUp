<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'profile.username',
                'required' => true,
                'help' => 'profile.username.help',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'profile.description',
                'required' => false,
            ])
            ->add('githubLink', UrlType::class, [
                'label' => 'profile.github_link',
                'help' => 'profile.github_link.help',
                'default_protocol' => 'https',
                'required' => false,
            ])
            ->add('picture', ProfilePictureType::class, [
                'label' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
