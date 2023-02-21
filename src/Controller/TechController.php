<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tech')]
final class TechController extends AbstractController
{
    #[Route('/', name: 'app_tech_index')]
    public function index(): Response
    {
        return $this->render('tech/index.html.twig');
    }

    #[Route('/create', name: 'app_tech_create')]
    public function create(): Response
    {
        return $this->render('tech/create.html.twig');
    }

    #[Route('/show/{slug}', name: 'app_tech_show')]
    public function show(): Response
    {
        return $this->render('tech/show.html.twig');
    }
}
