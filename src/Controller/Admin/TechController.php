<?php

namespace App\Controller\Admin;

use App\Entity\Tech;
use App\Manager\FlashManager;
use App\Repository\TechRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/techs')]
final class TechController extends AbstractController
{
    public function __construct(
        private readonly FlashManager $flashManager,
        private readonly TechRepository $techRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    #[Route('/', name: 'admin_tech', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $this->techRepository->createQueryBuilder('t'),
            $request->query->getInt('page', 1),
            5
        );

        $config = [
            'cols' => [
                'name' => [
                    'type' => 'text',
                    'label' => 'common.name',
                    'queryKey' => 't.name',
                ],
                'type.value' => [
                    'type' => 'text',
                    'label' => 'common.type',
                    'queryKey' => 't.type',
                ],
                'updatedAt' => [
                    'type' => 'date',
                    'label' => 'common.updated_at',
                    'queryKey' => 't.updatedAt',
                ],
                'createdAt' => [
                    'type' => 'date',
                    'label' => 'common.created_at',
                    'queryKey' => 't.createdAt',
                ],
                'actions' => [
                  'info' => [
                      'route' => 'admin_tech_show',
                      'routeParams' => [
                          'id' => static fn (Tech $tech): string => $tech->getId()->toBase32(),
                      ],
                      'icon' => 'eye',
                  ],
                  'accent' => [
                      'route' => 'admin_tech_edit',
                      'routeParams' => [
                          'id' => static fn (Tech $tech): string => $tech->getId()->toBase32(),
                      ],
                      'icon' => 'pencil',
                  ],
                ],
            ],
            'pagination' => $pagination,
        ];

        return $this->render('admin/tech/index.html.twig', ['config' => $config]);
    }

    #[Route('/{id}', name: 'admin_tech_show', methods: ['GET'])]
    public function show(Tech $tech): Response
    {
        return $this->render('admin/tech/show.html.twig', [
            'tech' => $tech,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_tech_edit', methods: ['GET', 'POST'])]
    public function edit(Tech $tech): Response
    {
        return $this->render('admin/tech/edit.html.twig', [
            'tech' => $tech,
        ]);
    }
}
