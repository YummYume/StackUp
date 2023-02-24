<?php

namespace App\Controller;

use App\Entity\Request as RequestEntity;
use App\Entity\Tech;
use App\Enum\ColorTypeEnum;
use App\Enum\TechTypeEnum;
use App\Form\TechType;
use App\Manager\FlashManager;
use App\Repository\TechRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tech')]
final class TechController extends AbstractController
{
    public function __construct(private readonly FlashManager $flashManager, private readonly TechRepository $techRepository)
    {
    }

    #[Route('/', name: 'app_tech_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('tech/index.html.twig');
    }

    #[Route('/show/{slug}', name: 'app_tech_show', methods: ['GET'])]
    public function show(Tech $tech): Response
    {
        return $this->render('tech/show.html.twig', [
            'tech' => $tech,
        ]);
    }

    #[Route('/type', name: 'app_tech_choose_type', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function selectType(): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser());

        if (null !== $tech) {
            return $this->redirectToRoute('app_tech_create', [
                'typeParam' => $tech->getType()->value,
            ]);
        }

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
    public function create(Request $request, string $typeParam): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser());
        $type = TechTypeEnum::from($typeParam);

        if (null !== $tech && $type !== $tech->getType()) {
            return $this->redirectToRoute('app_tech_create', [
                'typeParam' => $tech->getType()->value,
            ]);
        }

        if (null === $tech) {
            $tech = new Tech();
        }

        $tech->setType($type);
        $form = $this->createForm(TechType::class, $tech)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $tech->setRequest(new RequestEntity());
                $this->techRepository->save($tech, true);

                $this->flashManager->flash(ColorTypeEnum::Success->value, 'flash.tech.saved');

                return $this->redirectToRoute('app_tech_review_and_publish');
            }

            $this->flashManager->flash(ColorTypeEnum::Error->value, 'flash.common.invalid_form');
        }

        return $this->render('tech/create.html.twig', [
            'form' => $form,
            'type' => $type,
        ]);
    }

    #[Route('/review-and-publish', name: 'app_tech_review_and_publish', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function reviewAndPublish(Request $request): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser());

        if (null === $tech) {
            return $this->redirectToRoute('app_tech_choose_type');
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
}
