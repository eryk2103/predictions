<?php

namespace App\Controller;

use App\CompetitionService;
use App\Entity\Game;
use App\Form\GameType;
use App\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService        $service,
        private readonly CompetitionService $competitionService)
    {
    }

    private const PER_PAGE = 20;

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
        $total = count($paginator);
        $lastPage = max(1, (int) ceil($total / self::PER_PAGE));
        $pageLinks = $this->buildPageLinks($page, $lastPage);

        return $this->render('game/index.twig', [
            'games' => $paginator,
            'competitions' => $this->competitionService->getAll(),
            'phases' => $phases,
            'selectedCompetition' => $competitionId,
            'selectedPhase' => $phaseId,
            'page' => $page,
            'lastPage' => $lastPage,
            'total' => $total,
            'pageLinks' => $pageLinks,
        ]);
    }

    /** @return array<int|null> — null means ellipsis */
    private function buildPageLinks(int $page, int $lastPage): array
    {
        $show = array_fill_keys([1, $lastPage], true);
        for ($i = max(1, $page - 2); $i <= min($lastPage, $page + 2); $i++) {
            $show[$i] = true;
        }
        ksort($show);

        $links = [];
        $prev = 0;
        foreach (array_keys($show) as $p) {
            if ($p - $prev > 1) {
                $links[] = null;
            }
            $links[] = $p;
            $prev = $p;
        }

        return $links;
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

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'game_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('game/show.twig', [
            'game' => $this->service->get($id),
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

            return $this->redirectToRoute('game_show', ['id' => $id]);
        }

        return $this->render('game/edit.twig', [
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

        return $this->redirectToRoute('game_index');
    }
}
