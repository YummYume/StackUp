<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class CategoriesAutocompleteField extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $appSearchSanitizer, private readonly TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Category::class,
            'placeholder' => 'tech.categories.placeholder',
            'multiple' => true,
            'required' => false,
            'choice_label' => function (Category $category): string {
                $name = $category->getName();

                if ($category->isOfficial()) {
                    $name = sprintf('<span class="text-primary">%s</span>', $name);
                }

                return $this->appSearchSanitizer->sanitize($name);
            },
            'tom_select_options' => [
                'maxItems' => 3,
                'placeholder' => $this->translator->trans('tech.categories.placeholder'),
                'hidePlaceholder' => true,
            ],
            'query_builder' => static function (CategoryRepository $categoryRepository): QueryBuilder {
                return $categoryRepository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
            },
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
