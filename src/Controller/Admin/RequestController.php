<?php

namespace App\Controller\Admin;

use App\Entity\Request as RequestUser;
use App\Manager\FlashManager;
use App\Repository\RequestRepository as RequestUserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/requests')]
final class RequestController extends AbstractController
{
    public function __construct(
        private readonly FlashManager $flashManager,
        private readonly RequestUserRepository $requestUserRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    #[Route('/', name: 'admin_request', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $this->requestUserRepository
                ->createQueryBuilder('r')
                ->leftJoin('r.tech', 't'),
            $request->query->getInt('page', 1),
            5
        );

        $config = [
            'cols' => [
                'status.value' => [
                    'type' => 'text',
                    'label' => 'common.status',
                    'queryKey' => 'r.status',
                ],
                'tech.name' => [
                    'type' => 'text',
                    'label' => 'common.name',
                    'queryKey' => 't.name',
                ],
                'votes' => [
                    'type' => 'count',
                    'label' => 'request.votes.number',
                    'queryKey' => 't.votes',
                ],
                'updatedAt' => [
                    'type' => 'date',
                    'label' => 'common.updated_at',
                    'queryKey' => 'r.updatedAt',
                ],
                'createdAt' => [
                    'type' => 'date',
                    'label' => 'common.created_at',
                    'queryKey' => 'r.createdAt',
                ],
                'actions' => [
                  'info' => [
                      'route' => 'admin_request_show',
                      'routeParams' => [
                          'id' => static fn (RequestUser $requestUser): string => $requestUser->getId()->toBase32(),
                      ],
                      'icon' => 'eye',
                  ],
                ],
            ],
            'pagination' => $pagination,
        ];

        return $this->render('admin/request/index.html.twig', ['config' => $config]);
    }

    #[Route('/{id}', name: 'admin_request_show', methods: ['GET'])]
    public function show(RequestUser $requestUser): Response
    {
        return $this->render('admin/request/show.html.twig', [
            'request' => $requestUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_request_edit', methods: ['GET', 'POST'])]
    public function edit(RequestUser $requestUser): Response
    {
        return $this->render('admin/request/edit.html.twig', [
            'request' => $requestUser,
        ]);
    }
}
