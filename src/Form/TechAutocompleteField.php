<?php

namespace App\Form;

use App\Entity\Tech;
use App\Repository\TechRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class TechAutocompleteField extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $appSearchSanitizer, private readonly TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Tech::class,
            'placeholder' => 'tech.depends_on.placeholder',
            'required' => false,
            'choice_label' => function (Tech $tech): string {
                $name = $tech->getName();

                if ($tech->isOfficial()) {
                    $name = sprintf('<span class="validated">%s</span>', $name);
                }

                return $this->appSearchSanitizer->sanitize($name);
            },
            'tom_select_options' => [
                'placeholder' => $this->translator->trans('tech.depends_on.placeholder'),
                'hidePlaceholder' => true,
            ],
            'query_builder' => static function (TechRepository $techRepository): QueryBuilder {
                return $techRepository->getPendingAndAcceptedTechs();
            },
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
