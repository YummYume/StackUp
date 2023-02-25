<?php

namespace App\Controller;

use App\Entity\Stack;
use App\Enum\ColorTypeEnum;
use App\Form\StackType;
use App\Manager\FlashManager;
use App\Repository\StackRepository;
use App\Security\Voter\StackVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/stack')]
final class StackController extends AbstractController
{
    public function __construct(
        private readonly FlashManager $flashManager,
        private readonly StackRepository $stackRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    #[Route('/', name: 'app_stack_index')]
    public function index(): Response
    {
        return $this->render('stack/index.html.twig', [
            'stacks' => $this->stackRepository->findRecentlyAddedStacks(50),
        ]);
    }

    #[Route('/create', name: 'app_stack_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        $stack = new Stack();

        $form = $this->createForm(StackType::class, $stack)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                /** @var User */
                $user = $this->getUser();

                $stack->setProfile($user->getProfile());

                $this->stackRepository->save($stack, true);
                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.created');

                return $this->redirectToRoute('app_stack_show', [
                    'slug_stack' => $stack->getSlug(),
                    'slug_profile' => $stack->getProfile()->getSlug(),
                ], Response::HTTP_SEE_OTHER);
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');

                return $this->render('stack/edit.html.twig', [
                    'stack' => $stack,
                    'form' => $form,
                    'isEditing' => false,
                ]);
            }
        }

        return $this->render('stack/edit.html.twig', [
            'stack' => $stack,
            'form' => $form,
            'isEditing' => false,
        ]);
    }

    #[Route('/{slug_profile}/{slug_stack}/delete', name: 'app_stack_delete', methods: ['POST'])]
    #[Entity('stack', expr: 'repository.findBySlugProfile(slug_profile, slug_stack)')]
    #[IsGranted(StackVoter::EDIT, subject: 'stack', statusCode: 403)]
    public function delete(Request $request, Stack $stack): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$stack->getId()->toBase32(), $request->request->get('_token'))) {
            $this->stackRepository->remove($stack, true);

            $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.deleted');
        } else {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        return $this->redirectToRoute('app_stack_index', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug_profile}/{slug_stack}/edit', name: 'app_stack_edit', methods: ['GET', 'POST'])]
    #[Entity('stack', expr: 'repository.findBySlugProfile(slug_profile, slug_stack)')]
    #[IsGranted(StackVoter::EDIT, subject: 'stack', statusCode: 403)]
    public function edit(Request $request, Stack $stack): Response
    {
        $form = $this->createForm(StackType::class, $stack)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->stackRepository->save($stack, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.updated',);
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('stack/stream/edit.stream.html.twig', [
                    'stack' => $stack,
                    'form' => $form->isValid() ? $this->createForm(StackType::class, $stack) : $form,
                    'isEditing' => true,
                ]);
            } elseif ($form->isValid()) {
                return $this->redirectToRoute('admin_stack_edit', [
                    'slug_stack' => $stack->getSlug(),
                    'slug_profile' => $stack->getProfile()->getSlug(),
                ], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('stack/edit.html.twig', [
            'stack' => $stack,
            'form' => $form,
            'isEditing' => true,
        ]);
    }

    #[Route('/{slug_profile}/{slug_stack}', name: 'app_stack_show', methods: ['GET'])]
    #[Entity('stack', expr: 'repository.findBySlugProfile(slug_profile, slug_stack)')]
    public function show(Stack $stack): Response
    {
        return $this->render('stack/show.html.twig', [
            'stack' => $stack,
        ]);
    }
}
