<?php

namespace App\Controller\Admin;

use App\CompetitionService;
use App\Entity\Competition;
use App\Form\CompetitionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/competition', name: 'admin_')]
class CompetitionController extends AbstractController
{
    public function __construct(private readonly CompetitionService $service) {}

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
            $this->service->save($competition);

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
            /** @var Competition $data */
            $data = $form->getData();
            $this->service->update(
                $competition,
                $data->getName(),
                $data->getShortName(),
                $data->getStartYear(),
                $data->getEndYear(),
                $data->getLogo(),
            );

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
