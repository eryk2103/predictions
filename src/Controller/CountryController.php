<?php

namespace App\Controller;

use App\CountryService;
use App\Entity\Country;
use App\Form\CountryType;
use App\PaginationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/country')]
class CountryController extends AbstractController
{
    use PaginationTrait;

    public function __construct(private readonly CountryService $service) {}

    #[Route('', name: 'country_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $paginator = $this->service->getAll($page, self::PER_PAGE);

        return $this->render('country/index.twig', [
            'countries' => $paginator,
            ...$this->paginationData(count($paginator), $page),
        ]);
    }

    #[Route('/new', name: 'country_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create($country->getName(), $country->getFlag() ?? '');

            return $this->redirectToRoute('country_index');
        }

        return $this->render('country/new.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'country_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->render('country/show.twig', [
            'country' => $this->service->get($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'country_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $country = $this->service->get($id);
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update($country, $country->getName(), $country->getFlag());

            return $this->redirectToRoute('country_show', ['id' => $id]);
        }

        return $this->render('country/edit.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'country_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        $country = $this->service->get($id);

        if ($this->isCsrfTokenValid('delete_country_' . $id, $request->getPayload()->getString('_token'))) {
            $this->service->delete($country);
        }

        return $this->redirectToRoute('country_index');
    }
}
