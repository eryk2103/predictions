<?php

namespace App\Controller\Admin;

use App\Service\CompetitionService;
use App\Entity\Game;
use App\Enum\GameStatusEnum;
use App\Form\GameLiveType;
use App\Form\GameType;
use App\Service\GameService;
use App\Service\PaginationTrait;
use App\Service\PredictionPointService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/game', name: 'admin_')]
class GameController extends AbstractController
{
    use PaginationTrait;

    public function __construct(
        private readonly GameService           $service,
        private readonly CompetitionService    $competitionService,
        private readonly PredictionPointService $pointService,
    ) {}

    #[Route('', name: 'game_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $competitionId = $request->query->get('competition') ?: null;
        $phaseId = $request->query->get('phase') ?: null;
        $page = max(1, $request->query->getInt('page', 1));

        $phases = null;
        if ($competitionId !== null) {
            $phases = $this->competitionService->get($competitionId)->getCompetitionPhases();
        }

        $paginator = $this->service->getAll($competitionId, $phaseId, $page, self::PER_PAGE);

        return $this->render('admin/game/index.twig', [
            'games' => $paginator,
            'competitions' => $this->competitionService->getAll(),
            'phases' => $phases,
            'selectedCompetition' => $competitionId,
            'selectedPhase' => $phaseId,
            ...$this->paginationData(count($paginator), $page),
        ]);
    }

    #[Route('/new', name: 'game_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create(
                $game->getCompetitionPhase(),
                $game->getStatus(),
                $game->getHomeTeam(),
                $game->getAwayTeam(),
                $game->getDatetime(),
                $game->getStadium(),
                $game->getHomeScore(),
                $game->getAwayScore(),
                $game->getHomePenaltyScore(),
                $game->getAwayPenaltyScore(),
            );

            return $this->redirectToRoute('admin_game_index');
        }

        return $this->render('admin/game/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'game_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('admin/game/show.twig', [
            'game' => $this->service->get($id),
        ]);
    }

    #[Route('/{id}/live', name: 'game_live', methods: ['GET', 'POST'])]
    public function live(int $id, Request $request): Response
    {
        $game = $this->service->get($id);
        $form = $this->createForm(GameLiveType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update(
                $game,
                status: $game->getStatus(),
                homeScore: $game->getHomeScore(),
                awayScore: $game->getAwayScore(),
                homePenaltyScore: $game->getHomePenaltyScore(),
                awayPenaltyScore: $game->getAwayPenaltyScore(),
            );

            if ($game->getStatus() === GameStatusEnum::FINISHED) {
                $this->pointService->calculateForGame($game);
            }

            return $this->redirectToRoute('admin_game_live', ['id' => $id]);
        }

        return $this->render('admin/game/live.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'game_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $game = $this->service->get($id);
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update(
                $game,
                $game->getCompetitionPhase(),
                $game->getStatus(),
                $game->getHomeTeam(),
                $game->getAwayTeam(),
                $game->getDatetime(),
                $game->getStadium(),
                $game->getHomeScore(),
                $game->getAwayScore(),
                $game->getHomePenaltyScore(),
                $game->getAwayPenaltyScore(),
            );

            return $this->redirectToRoute('admin_game_show', ['id' => $id]);
        }

        return $this->render('admin/game/edit.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'game_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $game = $this->service->get($id);

        if ($this->isCsrfTokenValid('delete_game_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($game);
        }

        return $this->redirectToRoute('admin_game_index');
    }
}
