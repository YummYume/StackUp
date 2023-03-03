<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\StackRepository;
use App\Repository\TechRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig');
    }

    #[Route('/trending-and-recent', name: 'app_homepage_trending_and_recent', methods: ['GET'], condition: "request.headers.get('Turbo-Frame') !== null")]
    public function getTrendingAndRecent(
        StackRepository $stackRepository,
        TechRepository $techRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $since = (new \DateTime())->modify('-1 week');

        return $this->render('homepage/frame/_trending_and_recent.html.twig', [
            'trendingTechs' => $techRepository->findTrendingTechs($since, maxResults: 5),
            'trendingCategories' => $categoryRepository->findTrendingCategories($since, maxResults: 5),
            'recentTechs' => $techRepository->findRecentlyAddedTechs(5),
            'recentStacks' => $stackRepository->findRecentlyAddedStacks(5),
        ]);
    }
}
