<?php

namespace App\Form;

use App\Entity\Tech;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TechLinksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach (Tech::ALLOWED_LINKS as $link) {
            $builder
                ->add(sprintf('%sLink', $link), UrlType::class, [
                    'label' => sprintf('tech.link.%s', $link),
                    'help' => sprintf('tech.link.%s.help', $link),
                    'property_path' => sprintf('[%s]', $link),
                    'default_protocol' => 'https',
                    'required' => false,
                    'error_bubbling' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
