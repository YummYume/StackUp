<?php

namespace App\Controller\Admin;

use App\Entity\Stack;
use App\Manager\FlashManager;
use App\Repository\StackRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/stacks')]
final class StackController extends AbstractController
{
    public function __construct(
        private readonly FlashManager $flashManager,
        private readonly StackRepository $stackRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    #[Route('/', name: 'admin_stack', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $this->stackRepository->createQueryBuilder('s'),
            $request->query->getInt('page', 1),
            5
        );

        $config = [
            'cols' => [
                'name' => [
                    'type' => 'text',
                    'label' => 'stack.name',
                    'queryKey' => 's.name',
                ],
                'actions' => [
                  'info' => [
                      'route' => 'admin_stack_show',
                      'routeParams' => [
                          'id' => static fn (Stack $stack): string => $stack->getId()->toBase32(),
                      ],
                      'icon' => 'eye',
                  ],
                  'accent' => [
                      'route' => 'admin_stack_edit',
                      'routeParams' => [
                          'id' => static fn (Stack $stack): string => $stack->getId()->toBase32(),
                      ],
                      'icon' => 'pencil',
                  ],
              ],
            ],
            'pagination' => $pagination,
        ];

        return $this->render('admin/stack/index.html.twig', ['config' => $config]);
    }

    #[Route('/{id}', name: 'admin_stack_show', methods: ['GET'])]
    public function show(Stack $stack): Response
    {
        return $this->render('admin/stack/show.html.twig', [
            'stack' => $stack,
        ]);
    }
}
