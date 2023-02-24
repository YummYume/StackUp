<?php

namespace App\Controller\Admin;

use App\Entity\Tech;
use App\Enum\ColorTypeEnum;
use App\Form\Admin\TechType;
use App\Manager\FlashManager;
use App\Repository\TechRepository;
use App\Security\Voter\UserVoter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

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
                'isOfficial' => [
                    'type' => 'bool',
                    'label' => 'common.official.female',
                    'queryKey' => 't.status',
                    'extra' => [
                        'icon' => true,
                    ],
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

    #[Route('/{id}', name: 'admin_tech_delete', methods: ['POST'])]
    #[IsGranted(UserVoter::DELETE, subject: 'tech', statusCode: 403)]
    public function delete(Request $request, Tech $tech): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$tech->getId()->toBase32(), $request->request->get('_token'))) {
            $this->techRepository->remove($tech, true);

            $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.deleted', translationDomain: 'admin');
        } else {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        return $this->redirectToRoute('admin_tech', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'admin_tech_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tech $tech): Response
    {
        $form = $this->createForm(TechType::class, $tech)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->techRepository->save($tech, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.updated', translationDomain: 'admin');
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('admin/tech/stream/edit.stream.html.twig', [
                    'tech' => $tech,
                    'form' => $form->isValid() ? $this->createForm(TechType::class, $tech) : $form,
                ]);
            } elseif ($form->isValid()) {
                return $this->redirectToRoute('admin_tech_edit', [
                    'id' => $tech->getId()->toBase32(),
                ], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/tech/edit.html.twig', [
            'tech' => $tech,
            'form' => $form,
        ]);
    }
}
