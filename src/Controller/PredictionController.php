<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Form\PredictionType;
use App\GameService;
use App\PaginationTrait;
use App\PredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/prediction')]
#[IsGranted('ROLE_USER')]
class PredictionController extends AbstractController
{
    use PaginationTrait;
    public function __construct(
        private readonly PredictionService $service,
        private readonly GameService $gameService,
    ) {}

    #[Route('', name: 'prediction_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));

        return $this->render('prediction/index.twig', [
            'predictions' => $this->service->getByUser($this->getUser(), $page, self::PER_PAGE),
            ...$this->paginationData($this->service->countByUser($this->getUser()), $page),
        ]);
    }

    #[Route('/new', name: 'prediction_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $prediction = new Prediction();

        $gameId = $request->query->getInt('game');
        if ($gameId) {
            $prediction->setGame($this->gameService->get($gameId));
        }

        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->service->findByUserAndGame($this->getUser(), $prediction->getGame());
            if ($existing !== null) {
                return $this->redirectToRoute('prediction_edit', ['id' => $existing->getId()]);
            }

            $this->service->create(
                $this->getUser(),
                $prediction->getGame(),
                $prediction->getHomeScore(),
                $prediction->getAwayScore(),
                $prediction->getHomePenalty(),
                $prediction->getAwayPenalty(),
            );

            return $this->redirectToRoute('prediction_index');
        }

        return $this->render('prediction/new.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'prediction_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $prediction = $this->service->get($id);
        if ($prediction->getPredictor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update(
                $prediction,
                $prediction->getHomeScore(),
                $prediction->getAwayScore(),
                $prediction->getHomePenalty(),
                $prediction->getAwayPenalty(),
            );

            return $this->redirectToRoute('prediction_index');
        }

        return $this->render('prediction/edit.twig', [
            'form' => $form,
            'prediction' => $prediction,
        ]);
    }

    #[Route('/{id}/delete', name: 'prediction_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $prediction = $this->service->get($id);
        if ($prediction->getPredictor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete_prediction_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($prediction);
        }

        return $this->redirectToRoute('prediction_index');
    }
}
