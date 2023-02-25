<?php

namespace App\Form;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class ProfilesAutocompleteField extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $appSearchSanitizer, private readonly TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Profile::class,
            'placeholder' => 'profile.choose',
            'required' => true,
            'choice_label' => function (Profile $profile) {
                if (!$profile->getUser()->isVerified()) {
                    return;
                }

                $username = sprintf('<span class="text-warning">%s</span>', $profile->getUsername());

                return $this->appSearchSanitizer->sanitize($username);
            },
            'tom_select_options' => [
                'maxItems' => 1,
                'placeholder' => $this->translator->trans('profile.choose', domain: 'messages'),
                'hidePlaceholder' => true,
                'allowEmptyOption' => false,
            ],
            'query_builder' => static function (ProfileRepository $ProfileRepository): QueryBuilder {
                return $ProfileRepository->createQueryBuilder('p')->orderBy('p.username', 'ASC');
            },
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
