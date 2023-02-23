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
            $this->voteRepository->createQueryBuilder('s'),
            $request->query->getInt('page', 1),
            5
        );

        $config = [
            'cols' => [
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
}
