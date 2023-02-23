<?php

namespace App\Controller\Admin;

use App\Entity\Vote;
use App\Manager\FlashManager;
use App\Repository\VoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/votes')]
final class VoteController extends AbstractController
{
    public function __construct(
        private readonly FlashManager $flashManager,
        private readonly VoteRepository $voteRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    #[Route('/', name: 'admin_vote', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $this->voteRepository
                ->createQueryBuilder('v')
                ->leftJoin('v.profile', 'p')
                ->leftJoin('v.request', 'r'),
            $request->query->getInt('page', 1),
            5
        );

        $config = [
            'cols' => [
                'upvote' => [
                    'type' => 'text',
                    'label' => 'vote.up_vote',
                    'queryKey' => 'v.upvote',
                ],
                'request.id' => [
                    'type' => 'text',
                    'label' => 'request.id',
                    'queryKey' => 'r.id',
                ],
                'profile.username' => [
                    'type' => 'text',
                    'label' => 'common.created_by',
                    'queryKey' => 'p.username',
                ],
                'updatedAt' => [
                    'type' => 'date',
                    'label' => 'common.updated_at',
                    'queryKey' => 'p.updatedAt',
                ],
                'createdAt' => [
                    'type' => 'date',
                    'label' => 'common.created_at',
                    'queryKey' => 'p.createdAt',
                ],
                'actions' => [
                  'info' => [
                      'route' => 'admin_vote_show',
                      'routeParams' => [
                          'id' => static fn (Vote $vote): string => $vote->getId()->toBase32(),
                      ],
                      'icon' => 'eye',
                  ],
                  'accent' => [
                      'route' => 'admin_vote_edit',
                      'routeParams' => [
                          'id' => static fn (Vote $vote): string => $vote->getId()->toBase32(),
                      ],
                      'icon' => 'pencil',
                  ],
                ],
            ],
            'pagination' => $pagination,
        ];

        return $this->render('admin/vote/index.html.twig', ['config' => $config]);
    }

    #[Route('/{id}', name: 'admin_vote_show', methods: ['GET'])]
    public function show(Vote $vote): Response
    {
        return $this->render('admin/vote/show.html.twig', [
            'vote' => $vote,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_vote_edit', methods: ['GET', 'POST'])]
    public function edit(Vote $vote): Response
    {
        return $this->render('admin/vote/edit.html.twig', [
            'vote' => $vote,
        ]);
    }
}
