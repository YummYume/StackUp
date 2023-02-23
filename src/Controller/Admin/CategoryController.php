<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Manager\FlashManager;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/categories')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly FlashManager $flashManager,
        private readonly CategoryRepository $categoryRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    #[Route('/', name: 'admin_category', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $this->categoryRepository->createQueryBuilder('c'),
            $request->query->getInt('page', 1),
            5
        );

        $config = [
            'cols' => [
                'actions' => [
                  'info' => [
                      'route' => 'admin_category_show',
                      'routeParams' => [
                          'id' => static fn (Category $category): string => $category->getId()->toBase32(),
                      ],
                      'icon' => 'eye',
                  ],
                  'accent' => [
                      'route' => 'admin_category_edit',
                      'routeParams' => [
                          'id' => static fn (Category $category): string => $category->getId()->toBase32(),
                      ],
                      'icon' => 'pencil',
                  ],
              ],
            ],
            'pagination' => $pagination,
        ];

        return $this->render('admin/category/index.html.twig', ['config' => $config]);
    }

    #[Route('/{id}', name: 'admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }
}
