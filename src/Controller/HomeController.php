<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\PredictionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly PredictionRepository $predictionRepository,
    ) {}

    #[Route('', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        $games = $this->gameRepository->findByCurrentPhases();

        $predictions = [];
        if ($this->getUser()) {
            $predictions = $this->predictionRepository->findByUserForGames($this->getUser(), $games);
        }

        return $this->render('home/index.html.twig', [
            'games' => $games,
            'predictions' => $predictions,
        ]);
    }
}
