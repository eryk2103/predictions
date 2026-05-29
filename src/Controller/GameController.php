<?php

namespace App\Controller;

use App\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    public function __construct(private readonly GameService $service) {}

    #[Route('/{id}', name: 'game_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('game/show.twig', [
            'game' => $this->service->get($id),
        ]);
    }
}
