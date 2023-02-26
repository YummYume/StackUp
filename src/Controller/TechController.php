<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Request as RequestEntity;
use App\Entity\Tech;
use App\Entity\User;
use App\Entity\Vote;
use App\Enum\ColorTypeEnum;
use App\Enum\TechTypeEnum;
use App\Form\CategoryType;
use App\Form\TechType;
use App\Manager\FlashManager;
use App\Manager\VoteManager;
use App\Repository\CategoryRepository;
use App\Repository\TechRepository;
use App\Repository\VoteRepository;
use App\Security\Voter\TechVoter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/tech')]
final class TechController extends AbstractController
{
    public function __construct(private readonly FlashManager $flashManager, private readonly TechRepository $techRepository)
    {
    }

    #[Route('/', name: 'app_tech_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $this->techRepository->createQueryBuilder('t'),
            $request->query->getInt('page', 1),
            10,
            ['defaultSortFieldName' => 't.createdAt', 'defaultSortDirection' => 'desc']
        );

        return $this->render('tech/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/type', name: 'app_tech_choose_type', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function selectType(): Response
    {
        return $this->render('tech/select_type.html.twig', [
            'types' => TechTypeEnum::cases(),
        ]);
    }

    #[Route(
        '/create/{typeParam}',
        name: 'app_tech_create',
        methods: ['GET', 'POST'],
        requirements: ['type' => new EnumRequirement(TechTypeEnum::class)]
    )]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, string $typeParam, CategoryRepository $categoryRepository): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser()) ?: new Tech();
        $type = TechTypeEnum::from($typeParam);
        $tech->setType($type);
        $form = $this->createForm(TechType::class, $tech)->handleRequest($request);
        $categoryForm = $this->createForm(CategoryType::class, $category = new Category())->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $tech->setRequest(new RequestEntity());
                $this->techRepository->save($tech, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.tech.saved');

                return $this->redirectToRoute('app_tech_review_and_publish');
            }

            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
        } elseif ($categoryForm->isSubmitted()) {
            if ($categoryForm->isValid()) {
                $categoryRepository->save($category, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.create_category.success', [
                    'name' => $category->getName(),
                ]);
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('tech/stream/create_category.stream.html.twig', [
                    'categoryForm' => $categoryForm->isValid() ? $this->createForm(CategoryType::class) : $categoryForm,
                    'isValid' => $categoryForm->isValid(),
                ]);
            } elseif ($categoryForm->isValid()) {
                return $this->redirectToRoute('app_tech_create', status: Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('tech/create.html.twig', [
            'form' => $form,
            'categoryForm' => $categoryForm,
        ]);
    }

    #[Route('/review-and-publish', name: 'app_tech_review_and_publish', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function reviewAndPublish(Request $request, ValidatorInterface $validator): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser());

        if (null === $tech) {
            return $this->redirectToRoute('app_tech_choose_type');
        }

        $errors = $validator->validate($tech);

        if (\count($errors) > 0) {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.tech.invalid_form');

            return $this->redirectToRoute('app_tech_choose_type', [
                'typeParam' => $tech->getType()->value,
            ]);
        }

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($this->isCsrfTokenValid('publish-'.$tech->getSlug(), $request->get('_token'))) {
                $tech->getRequest()->setCreated(true);
                $this->techRepository->save($tech, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.tech.published');

                return $this->redirectToRoute('app_tech_show', [
                    'slug' => $tech->getSlug(),
                ]);
            }

            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        return $this->render('tech/review_and_publish.html.twig', [
            'tech' => $tech,
        ]);
    }

    #[Route('/{slug}', name: 'app_tech_show', methods: ['GET'])]
    public function show(Tech $tech): Response
    {
        return $this->render('tech/show.html.twig', [
            'tech' => $tech,
        ]);
    }

    #[Route('/{slug}/vote/{voteType<upvote|downvote>}', name: 'app_tech_vote', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function vote(Request $request, Tech $tech, VoteRepository $voteRepository, string $voteType, VoteManager $voteManager): Response
    {
        if ($this->isCsrfTokenValid('vote-'.$tech->getId()->toBase32(), $request->request->get('_token'))) {
            /** @var User */
            $user = $this->getUser();
            $vote = $tech->getProfileVote($user->getProfile());
            $isUpvote = 'upvote' === $voteType;

            if ($isUpvote) {
                if (null !== $vote && $vote->isUpvote()) {
                    $voteRepository->remove($vote, true);
                    $vote = null;
                } else {
                    if (null === $vote) {
                        $vote = (new Vote())
                            ->setRequest($tech->getRequest())
                            ->setProfile($user->getProfile())
                        ;
                        $tech->getRequest()->addVote($vote);
                    }

                    $vote->setUpvote(true);
                }
            } else {
                if (null !== $vote && !$vote->isUpvote()) {
                    $voteRepository->remove($vote, true);
                    $vote = null;
                } else {
                    if (null === $vote) {
                        $vote = (new Vote())
                            ->setRequest($tech->getRequest())
                            ->setProfile($user->getProfile())
                        ;
                        $tech->getRequest()->addVote($vote);
                    }

                    $vote->setUpvote(false);
                }
            }

            $voteManager->handleRequestStatus($tech->getRequest());

            if ($vote) {
                $voteRepository->save($vote, true);
            }
        } else {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render('tech/stream/vote.stream.html.twig', [
                'tech' => $tech,
                'vote' => $vote,
            ]);
        }

        return $this->redirectToRoute('app_tech_show', ['slug' => $tech->getSlug()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug}', name: 'app_tech_delete', methods: ['POST'])]
    #[IsGranted(TechVoter::DELETE, subject: 'tech', statusCode: 403)]
    public function delete(Request $request, Tech $tech): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$tech->getId()->toBase32(), $request->request->get('_token'))) {
            $this->techRepository->remove($tech, true);

            $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.deleted');
        } else {
            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_csrf');
        }

        return $this->redirectToRoute('app_tech_index', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug}/edit', name: 'app_tech_edit', methods: ['GET', 'POST'])]
    #[IsGranted(TechVoter::EDIT, subject: 'tech', statusCode: 403)]
    public function edit(Request $request, Tech $tech): Response
    {
        $form = $this->createForm(TechType::class, $tech, ['can_edit_type' => true])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->techRepository->save($tech, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.common.updated');
            } else {
                $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('tech/stream/edit.stream.html.twig', [
                    'tech' => $tech,
                    'form' => $form->isValid() ? $this->createForm(TechType::class, $tech, ['can_edit_type' => true]) : $form,
                    'isEditing' => true,
                ]);
            } elseif ($form->isValid()) {
                return $this->redirectToRoute('app_tech_edit', [
                    'slug' => $tech->getSlug(),
                ], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('tech/edit.html.twig', [
            'tech' => $tech,
            'form' => $form,
            'isEditing' => true,
        ]);
    }
}
