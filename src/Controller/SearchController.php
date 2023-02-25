<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Stack;
use App\Entity\Tech;
use App\Enum\SearchTypeEnum;
use Meilisearch\Bundle\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search')]
final class SearchController extends AbstractController
{
    #[Route('/', name: 'app_search', methods: ['GET'])]
    public function globalSearch(Request $request, SearchService $searchService): Response
    {
        $query = trim($request->get('q', ''));
        $page = (int) $request->get('p', 1);
        $type = SearchTypeEnum::tryFrom($request->get('t', SearchTypeEnum::Techs->value)) ?? SearchTypeEnum::Techs;
        $searchAttributes = match ($type) {
            SearchTypeEnum::Techs => [
                'class' => Tech::class,
                'nameProperty' => 'name',
                'descProperty' => 'description',
                'slugProperty' => 'slug',
                'route' => 'app_tech_show',
                'routeParam' => 'slug',
            ],
            SearchTypeEnum::Stacks => [
                'class' => Stack::class,
                'nameProperty' => 'name',
                'descProperty' => 'description',
                'slugProperty' => 'slug',
                'route' => 'app_stack_show',
                'routeParam' => 'slug',
            ],
            SearchTypeEnum::Profiles => [
                'class' => Profile::class,
                'nameProperty' => 'username',
                'descProperty' => 'description',
                'slugProperty' => 'slug',
                'route' => 'app_profile_show',
                'routeParam' => 'slug',
            ],
            default => [
                'class' => Profile::class,
                'nameProperty' => 'username',
                'descProperty' => 'description',
                'slugProperty' => 'slug',
                'route' => 'app_profile_show',
                'routeParam' => 'slug',
            ],
        };

        $search = !empty($query) ? $searchService->rawSearch($searchAttributes['class'], $query, [
            ...SearchTypeEnum::getSearchOptions($type),
            'hitsPerPage' => 10,
            'page' => $page,
            'highlightPreTag' => '<em class="bg-warning dark:bg-secondary">',
        ]) : null;

        return $this->render('search/index.html.twig', [
            'search' => $search,
            'type' => $type->value,
            'attributes' => $searchAttributes,
        ]);
    }
}
