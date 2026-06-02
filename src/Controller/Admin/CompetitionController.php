<?php

namespace App\Controller\Admin;

use App\Dto\CreateCompetitionDto;
use App\Dto\UpdateCompetitionDto;
use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Service\CompetitionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/competition', name: 'admin_')]
class CompetitionController extends AbstractController
{
    public function __construct(private readonly CompetitionServiceInterface $service) {}

    #[Route('', name: 'competition_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/competition/index.twig', [
            'competitions' => $this->service->getAll(),
        ]);
    }

    #[Route('/new', name: 'competition_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create(new CreateCompetitionDto(
                $competition->getName(),
                $competition->getShortName(),
                $competition->getStartYear(),
                $competition->getEndYear(),
                $competition->getLogo(),
            ));

            return $this->redirectToRoute('admin_competition_index');
        }

        return $this->render('admin/competition/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'competition_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('admin/competition/show.twig', [
            'competition' => $this->service->get($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'competition_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $competition = $this->service->get($id);
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update($competition, new UpdateCompetitionDto(
                $competition->getName(),
                $competition->getShortName(),
                $competition->getStartYear(),
                $competition->getEndYear(),
                $competition->getLogo(),
            ));

            return $this->redirectToRoute('admin_competition_show', ['id' => $id]);
        }

        return $this->render('admin/competition/edit.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'competition_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $competition = $this->service->get($id);

        if ($this->isCsrfTokenValid('delete_competition_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($competition);
        }

        return $this->redirectToRoute('admin_competition_index');
    }
}
