<?php

namespace App\Components;

use App\Entity\Profile;
use App\Entity\Stack;
use App\Entity\Tech;
use App\Enum\SearchTypeEnum;
use Meilisearch\Bundle\SearchService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent('global_search')]
final class GlobalSearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private readonly SearchService $searchService)
    {
    }

    #[ExposeInTemplate]
    public function getSearchResult(): ?array
    {
        if (empty($this->query)) {
            return null;
        }

        $results = [];

        $techs = $this->searchService->rawSearch(Tech::class, $this->query, [
            ...SearchTypeEnum::getSearchOptions(SearchTypeEnum::Techs),
            'highlightPreTag' => '<em class="bg-warning dark:bg-secondary">',
            'limit' => 5,
        ]);

        if (!empty($techs['hits'])) {
            $results['techs'] = [
                'results' => $techs,
                'nameProperty' => 'name',
                'descProperty' => 'description',
                'slugProperty' => ['slug' => 'slug'],
                'route' => 'app_tech_show',
                'routeParam' => ['slug'],
            ];
        }

        $stacks = $this->searchService->rawSearch(Stack::class, $this->query, [
            ...SearchTypeEnum::getSearchOptions(SearchTypeEnum::Stacks),
            'highlightPreTag' => '<em class="bg-warning dark:bg-secondary">',
            'limit' => 5,
        ]);

        if (!empty($stacks['hits'])) {
            $results['stacks'] = [
                'results' => $stacks,
                'nameProperty' => 'name',
                'descProperty' => 'description',
                'slugProperty' => [
                    'slug_stack' => 'slug',
                    'slug_profile' => 'profileSlug',
                ],
                'route' => 'app_stack_show',
                'routeParam' => ['slug_stack', 'slug_profile'],
            ];
        }

        $profiles = $this->searchService->rawSearch(Profile::class, $this->query, [
            ...SearchTypeEnum::getSearchOptions(SearchTypeEnum::Profiles),
            'highlightPreTag' => '<em class="bg-warning dark:bg-secondary">',
            'limit' => 5,
        ]);

        if (!empty($profiles['hits'])) {
            $results['profiles'] = [
                'results' => $profiles,
                'nameProperty' => 'username',
                'descProperty' => 'description',
                'slugProperty' => ['slug' => 'slug'],
                'route' => 'app_profile_show',
                'routeParam' => ['slug'],
            ];
        }

        return $results;
    }
}
