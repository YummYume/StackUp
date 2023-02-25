<?php

namespace App\Controller;

use App\Repository\StackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stack')]
final class StackController extends AbstractController
{
    #[Route('/', name: 'app_stack_index')]
    public function index(StackRepository $stackRepository): Response
    {
        return $this->render('stack/index.html.twig', [
            'stacks' => $stackRepository->findRecentlyAddedStacks(50),
        ]);
    }

    #[Route('/create', name: 'app_stack_create')]
    public function create(): Response
    {
        return $this->render('stack/create.html.twig');
    }

    #[Route('/show/{slug}', name: 'app_stack_show')]
    public function show(): Response
    {
        return $this->render('stack/show.html.twig');
    }
}
