<?php

namespace App\Controller\Admin;

use App\Dto\CreateTeamDto;
use App\Dto\UpdateTeamDto;
use App\Entity\Team;
use App\Form\TeamType;
use App\Controller\Trait\PaginationTrait;
use App\Service\TeamServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/team', name: 'admin_')]
class TeamController extends AbstractController
{
    use PaginationTrait;

    public function __construct(private readonly TeamServiceInterface $service) {}

    #[Route('', name: 'team_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $paginator = $this->service->getAll($page, self::PER_PAGE);

        return $this->render('admin/team/index.twig', [
            'teams' => $paginator,
            ...$this->paginationData(count($paginator), $page),
        ]);
    }

    #[Route('/new', name: 'team_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create(new CreateTeamDto(
                $team->getName(),
                $team->getShortName(),
                $team->getCode(),
                $team->getLogo(),
                $team->getCity(),
                $team->getCountry(),
                $team->getStadium(),
            ));

            return $this->redirectToRoute('admin_team_index');
        }

        return $this->render('admin/team/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'team_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('admin/team/show.twig', [
            'team' => $this->service->get($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'team_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $team = $this->service->get($id);
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update($team, new UpdateTeamDto(
                $team->getName(),
                $team->getShortName(),
                $team->getCode(),
                $team->getLogo(),
                $team->getCity(),
                $team->getCountry(),
                $team->getStadium(),
            ));

            return $this->redirectToRoute('admin_team_show', ['id' => $id]);
        }

        return $this->render('admin/team/edit.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'team_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $team = $this->service->get($id);

        if ($this->isCsrfTokenValid('delete_team_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($team);
        }

        return $this->redirectToRoute('admin_team_index');
    }
}
