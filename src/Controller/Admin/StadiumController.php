<?php

namespace App\Controller\Admin;

use App\Dto\CreateStadiumDto;
use App\Dto\UpdateStadiumDto;
use App\Entity\Stadium;
use App\Form\StadiumType;
use App\Controller\Trait\PaginationTrait;
use App\Service\StadiumServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/stadium', name: 'admin_')]
class StadiumController extends AbstractController
{
    use PaginationTrait;

    public function __construct(private readonly StadiumServiceInterface $service) {}

    #[Route('', name: 'stadium_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $paginator = $this->service->getAll($page, self::PER_PAGE);

        return $this->render('admin/stadium/index.twig', [
            'stadiums' => $paginator,
            ...$this->paginationData(count($paginator), $page),
        ]);
    }

    #[Route('/new', name: 'stadium_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $stadium = new Stadium();
        $form = $this->createForm(StadiumType::class, $stadium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create(new CreateStadiumDto(
                $stadium->getName(),
                $stadium->getCity(),
                $stadium->getCapacity(),
                $stadium->getCountry(),
            ));

            return $this->redirectToRoute('admin_stadium_index');
        }

        return $this->render('admin/stadium/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'stadium_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('admin/stadium/show.twig', [
            'stadium' => $this->service->get($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'stadium_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $stadium = $this->service->get($id);
        $form = $this->createForm(StadiumType::class, $stadium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update($stadium, new UpdateStadiumDto(
                $stadium->getName(),
                $stadium->getCity(),
                $stadium->getCapacity(),
                $stadium->getCountry(),
            ));

            return $this->redirectToRoute('admin_stadium_show', ['id' => $id]);
        }

        return $this->render('admin/stadium/edit.twig', [
            'stadium' => $stadium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'stadium_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $stadium = $this->service->get($id);

        if ($this->isCsrfTokenValid('delete_stadium_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($stadium);
        }

        return $this->redirectToRoute('admin_stadium_index');
    }
}
