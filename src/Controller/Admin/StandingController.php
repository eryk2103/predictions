<?php

namespace App\Controller\Admin;

use App\Dto\CreateStandingDto;
use App\Dto\UpdateStandingDto;
use App\Entity\Standing;
use App\Enum\PhaseTypeEnum;
use App\Form\StandingType;
use App\Service\CompetitionServiceInterface;
use App\Service\GameServiceInterface;
use App\Service\StandingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/standing', name: 'admin_')]
class StandingController extends AbstractController
{
    public function __construct(
        private readonly StandingServiceInterface $service,
        private readonly CompetitionServiceInterface $competitionService,
        private readonly GameServiceInterface $gameService,
    ) {}

    #[Route('', name: 'standing_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $competitionId = $request->query->get('competition') ?: null;
        $phases = [];
        if ($competitionId !== null) {
            $competition = $this->competitionService->get($competitionId);
            $phases = array_filter($competition->getCompetitionPhases()->toArray(),
                fn($phase) => $phase->getType() === PhaseTypeEnum::KNOCKOUT
            );
        }

        $groupedGames = $phases ? $this->gameService->getGroupedByPhase($competitionId) : [];

        return $this->render('admin/standing/index.twig', [
            'standings' => $competitionId !== null ? $this->service->getAll($competitionId) : [],
            'competitions' => $this->competitionService->getAll(),
            'selectedCompetition' => $competitionId,
            'phases' => $phases,
            'groupedGames' => $groupedGames,
        ]);
    }

    #[Route('/new', name: 'standing_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $standing = new Standing();
        $form = $this->createForm(StandingType::class, $standing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create(new CreateStandingDto(
                $standing->getCompetition(),
                $standing->getTeam(),
                $standing->getPosition(),
                $standing->getPlayed(),
                $standing->getWon(),
                $standing->getDrawn(),
                $standing->getLost(),
                $standing->getGoals(),
                $standing->getGoalsAgainst(),
                $standing->getPoints(),
            ));

            return $this->redirectToRoute('admin_standing_index');
        }

        return $this->render('admin/standing/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'standing_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('admin/standing/show.twig', [
            'standing' => $this->service->get($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'standing_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $standing = $this->service->get($id);
        $form = $this->createForm(StandingType::class, $standing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update($standing, new UpdateStandingDto(
                $standing->getCompetition(),
                $standing->getTeam(),
                $standing->getPosition(),
                $standing->getPlayed(),
                $standing->getWon(),
                $standing->getDrawn(),
                $standing->getLost(),
                $standing->getGoals(),
                $standing->getGoalsAgainst(),
                $standing->getPoints(),
            ));

            return $this->redirectToRoute('admin_standing_show', ['id' => $id]);
        }

        return $this->render('admin/standing/edit.twig', [
            'standing' => $standing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'standing_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $standing = $this->service->get($id);

        if ($this->isCsrfTokenValid('delete_standing_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($standing);
        }

        return $this->redirectToRoute('admin_standing_index');
    }
}
