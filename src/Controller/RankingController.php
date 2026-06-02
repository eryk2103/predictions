<?php

namespace App\Controller;

use App\Service\CompetitionServiceInterface;
use App\Service\RankingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rankings')]
class RankingController extends AbstractController
{
    public function __construct(
        private readonly RankingServiceInterface $service,
        private readonly CompetitionServiceInterface $competitionService,
    ) {}

    #[Route('', name: 'ranking_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $competitionId = $request->query->get('competition') ?: null;
        $phaseId = $request->query->get('phase') ?: null;

        $phases = null;
        if ($competitionId !== null) {
            $phases = $this->competitionService->get($competitionId)->getCompetitionPhases();
        }

        return $this->render('ranking/index.twig', [
            'rankings' => $this->service->getRankings($competitionId, $phaseId),
            'competitions' => $this->competitionService->getAll(),
            'phases' => $phases,
            'selectedCompetition' => $competitionId,
            'selectedPhase' => $phaseId,
        ]);
    }
}
