<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepositoryInterface;
use App\Repository\PredictionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    public function __construct(
        private readonly GameRepositoryInterface $gameRepository,
        private readonly PredictionRepositoryInterface $predictionRepository,
    ) {}

    #[Route('', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        $games = $this->gameRepository->findByCurrentPhases();
        $gamesGroupedByPhases = [];
        foreach ($games as $game) {
            $phase = $game->getCompetitionPhase();
            $name = $phase->getCompetition()->getName() . ' - ' . $phase->getName();
            $gamesGroupedByPhases[$name][] = $game;
        }

        $predictions = [];
        if ($this->getUser()) {
            $predictions = $this->predictionRepository->findByUserForGames($this->getUser(), $games);
        }

        return $this->render('home/index.html.twig', [
            'gamesGrouped' => $gamesGroupedByPhases,
            'predictions' => $predictions,
        ]);
    }
}
