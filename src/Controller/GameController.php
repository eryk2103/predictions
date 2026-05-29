<?php

namespace App\Controller;

use App\GameService;
use App\Repository\PredictionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService $service,
        private readonly PredictionRepository $predictionRepository,
    ) {}

    #[Route('/{id}', name: 'game_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $game = $this->service->get($id);

        $prediction = null;
        if ($this->getUser()) {
            $prediction = $this->predictionRepository->findByUserAndGame($this->getUser(), $game);
        }

        return $this->render('game/show.twig', [
            'game' => $game,
            'prediction' => $prediction,
        ]);
    }
}
