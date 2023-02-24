<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\Admin\CategoryType;
use App\Enum\ColorTypeEnum;
use App\Manager\FlashManager;
use App\Repository\CategoryRepository;
use App\Security\Voter\UserVoter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

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
                'name' => [
                    'type' => 'text',
                    'label' => 'common.name',
                    'queryKey' => 'c.name',
                ],
                'updatedAt' => [
                    'type' => 'date',
                    'label' => 'common.updated_at',
                    'queryKey' => 'c.updatedAt',
                ],
                'createdAt' => [
                    'type' => 'date',
                    'label' => 'common.created_at',
                    'queryKey' => 'c.createdAt',
                ],
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

    #[Route('/{id}', name: 'admin_category_delete', methods: ['POST'])]
    #[IsGranted(UserVoter::DELETE, subject: 'category', statusCode: 403)]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$category->getId()->toBase32(), $request->request->get('_token'))) {
            $this->categoryRepository->remove($category, true);

            $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.deleted', translationDomain: 'admin');
        } else {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        return $this->redirectToRoute('admin_category', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->categoryRepository->save($category, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.updated', translationDomain: 'admin');
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('admin/category/stream/edit.stream.html.twig', [
                    'category' => $category,
                    'form' => $form->isValid() ? $this->createForm(CategoryType::class, $category) : $form,
                ]);
            } elseif ($form->isValid()) {
                return $this->redirectToRoute('admin_category_edit', [
                    'id' => $category->getId()->toBase32(),
                ], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
}
