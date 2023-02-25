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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function create(Request $request, string $typeParam): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser()) ?: new Tech();
        $type = TechTypeEnum::from($typeParam);
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
    public function reviewAndPublish(Request $request, ValidatorInterface $validator): Response
    {
        $tech = $this->techRepository->findUnreleasedTechForUser($this->getUser());

        if (null === $tech) {
            return $this->redirectToRoute('app_tech_choose_type');
        }

        $errors = $validator->validate($tech);

        if (\count($errors) > 0) {
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
}
