<?php

namespace App\Service;

use App\Dto\CreateCompetitionDto;
use App\Dto\UpdateCompetitionDto;
use App\Entity\Competition;
use App\Exception\CompetitionNotFoundException;
use App\Repository\CompetitionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class CompetitionService implements CompetitionServiceInterface
{
    public function __construct(
        private readonly CompetitionRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(CreateCompetitionDto $dto): Competition
    {
        $competition = (new Competition())
            ->setName($dto->name)
            ->setShortName($dto->shortName)
            ->setStartYear($dto->startYear)
            ->setEndYear($dto->endYear)
            ->setLogo($dto->logo);

        $this->em->persist($competition);
        $this->em->flush();

        return $competition;
    }

    public function get(int $id): Competition
    {
        $competition = $this->repository->find($id);

        if ($competition === null) {
            throw new CompetitionNotFoundException($id);
        }

        return $competition;
    }

    /** @return Competition[] */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function update(Competition $competition, UpdateCompetitionDto $dto): Competition
    {
        $competition
            ->setName($dto->name)
            ->setShortName($dto->shortName)
            ->setStartYear($dto->startYear)
            ->setEndYear($dto->endYear)
            ->setLogo($dto->logo);

        $this->em->flush();

        return $competition;
    }

    public function delete(Competition $competition): void
    {
        $this->em->remove($competition);
        $this->em->flush();
    }
}
