<?php

namespace App\Controller\Admin;

use App\Entity\Stack;
use App\Enum\ColorTypeEnum;
use App\Form\Admin\StackType;
use App\Manager\FlashManager;
use App\Repository\StackRepository;
use App\Security\Voter\UserVoter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

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
                    'label' => 'common.name',
                    'queryKey' => 's.name',
                ],
                'description' => [
                    'type' => 'text',
                    'label' => 'common.description',
                    'queryKey' => 's.description',
                ],
                'updatedAt' => [
                    'type' => 'date',
                    'label' => 'common.updated_at',
                    'queryKey' => 's.updatedAt',
                ],
                'createdAt' => [
                    'type' => 'date',
                    'label' => 'common.created_at',
                    'queryKey' => 's.createdAt',
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

    #[Route('/new', name: 'admin_stack_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->form($request, new Stack(), false);
    }

    #[Route('/{id}', name: 'admin_stack_show', methods: ['GET'])]
    public function show(Stack $stack): Response
    {
        return $this->render('admin/stack/show.html.twig', [
            'stack' => $stack,
        ]);
    }

    #[Route('/{id}', name: 'admin_stack_delete', methods: ['POST'])]
    #[IsGranted(UserVoter::DELETE, subject: 'user', statusCode: 403)]
    public function delete(Request $request, Stack $stack): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$stack->getId()->toBase32(), $request->request->get('_token'))) {
            $this->stackRepository->remove($stack, true);

            $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.deleted', translationDomain: 'admin');
        } else {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        return $this->redirectToRoute('admin_stack', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'admin_stack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stack $stack): Response
    {
        return $this->form($request, $stack, true);
    }

    public function form(Request $request, Stack $stack, bool $isEditing): Response {
        $form = $this->createForm(StackType::class, $stack)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->stackRepository->save($stack, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.'.($isEditing ? 'updated' : 'created'), translationDomain: 'admin');
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat() && $isEditing) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('admin/stack/stream/edit.stream.html.twig', [
                    'stack' => $stack,
                    'form' => $form->isValid() ? $this->createForm(StackType::class, $stack) : $form,
                    'isEditing' => $isEditing,
                ]);
            } elseif ($form->isValid()) {
                return $this->redirectToRoute('admin_stack_edit', [
                    'id' => $stack->getId()->toBase32(),
                ], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/stack/edit.html.twig', [
            'stack' => $stack,
            'form' => $form,
            'isEditing' => $isEditing,
        ]);
    }
}
