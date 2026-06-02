<?php

namespace App\Service;

use App\Dto\CreateStadiumDto;
use App\Dto\UpdateStadiumDto;
use App\Entity\Stadium;
use App\Exception\StadiumNotFoundException;
use App\Repository\StadiumRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class StadiumService implements StadiumServiceInterface
{
    public function __construct(
        private readonly StadiumRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(CreateStadiumDto $dto): Stadium
    {
        $stadium = (new Stadium())
            ->setName($dto->name)
            ->setCity($dto->city)
            ->setCapacity($dto->capacity)
            ->setCountry($dto->country);

        $this->em->persist($stadium);
        $this->em->flush();

        return $stadium;
    }

    public function get(int $id): Stadium
    {
        $stadium = $this->repository->find($id);

        if ($stadium === null) {
            throw new StadiumNotFoundException($id);
        }

        return $stadium;
    }

    public function getAll(int $page = 1, int $perPage = 20): Paginator
    {
        return $this->repository->findPaginated($page, $perPage);
    }

    public function update(Stadium $stadium, UpdateStadiumDto $dto): Stadium
    {
        $stadium
            ->setName($dto->name)
            ->setCity($dto->city)
            ->setCapacity($dto->capacity)
            ->setCountry($dto->country);

        $this->em->flush();

        return $stadium;
    }

    public function delete(Stadium $stadium): void
    {
        $this->em->remove($stadium);
        $this->em->flush();
    }
}
