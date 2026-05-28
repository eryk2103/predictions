<?php

namespace App\Controller;

use App\CompetitionPhaseService;
use App\CompetitionService;
use App\Entity\CompetitionPhase;
use App\Form\CompetitionPhaseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/competition/{competitionId}/phase')]
class CompetitionPhaseController extends AbstractController
{
    public function __construct(
        private readonly CompetitionService $competitionService,
        private readonly CompetitionPhaseService $phaseService,
    ) {}

    #[Route('/new', name: 'competition_phase_new', methods: ['GET', 'POST'])]
    public function new(int $competitionId, Request $request): Response
    {
        $competition = $this->competitionService->get($competitionId);
        $phase = new CompetitionPhase();
        $form = $this->createForm(CompetitionPhaseType::class, $phase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->phaseService->create(
                $competition,
                $phase->getName(),
                $phase->getType(),
                $phase->getSequence(),
            );

            return $this->redirectToRoute('competition_show', ['id' => $competitionId]);
        }

        return $this->render('competition_phase/new.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'competition_phase_edit', methods: ['GET', 'POST'])]
    public function edit(int $competitionId, int $id, Request $request): Response
    {
        $competition = $this->competitionService->get($competitionId);
        $phase = $this->phaseService->get($id);
        $form = $this->createForm(CompetitionPhaseType::class, $phase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->phaseService->update(
                $phase,
                $phase->getName(),
                $phase->getType(),
                $phase->getSequence(),
            );

            return $this->redirectToRoute('competition_show', ['id' => $competitionId]);
        }

        return $this->render('competition_phase/edit.twig', [
            'competition' => $competition,
            'phase' => $phase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'competition_phase_delete', methods: ['POST'])]
    public function delete(int $competitionId, int $id, Request $request): Response
    {
        $phase = $this->phaseService->get($id);

        if ($this->isCsrfTokenValid('delete_phase_' . $id, $request->getPayload()->getString('_token'))) {
            $this->phaseService->delete($phase);
        }

        return $this->redirectToRoute('competition_show', ['id' => $competitionId]);
    }
}
