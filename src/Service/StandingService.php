<?php

namespace App\Service;

use App\Dto\CreateStandingDto;
use App\Dto\UpdateStandingDto;
use App\Entity\Standing;
use App\Exception\StandingNotFoundException;
use App\Repository\StandingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class StandingService implements StandingServiceInterface
{
    public function __construct(
        private readonly StandingRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(CreateStandingDto $dto): Standing
    {
        $standing = (new Standing())
            ->setCompetition($dto->competition)
            ->setTeam($dto->team)
            ->setPosition($dto->position)
            ->setPlayed($dto->played)
            ->setWon($dto->won)
            ->setDrawn($dto->drawn)
            ->setLost($dto->lost)
            ->setGoals($dto->goals)
            ->setGoalsAgainst($dto->goalsAgainst)
            ->setPoints($dto->points);

        $this->em->persist($standing);
        $this->em->flush();

        return $standing;
    }

    public function get(int $id): Standing
    {
        $standing = $this->repository->find($id);

        if ($standing === null) {
            throw new StandingNotFoundException($id);
        }

        return $standing;
    }

    /** @return Standing[] */
    public function getAll(?int $competitionId = null): array
    {
        return $this->repository->findByCompetition($competitionId);
    }

    public function update(Standing $standing, UpdateStandingDto $dto): Standing
    {
        $standing
            ->setCompetition($dto->competition)
            ->setTeam($dto->team)
            ->setPosition($dto->position)
            ->setPlayed($dto->played)
            ->setWon($dto->won)
            ->setDrawn($dto->drawn)
            ->setLost($dto->lost)
            ->setGoals($dto->goals)
            ->setGoalsAgainst($dto->goalsAgainst)
            ->setPoints($dto->points);

        $this->em->flush();

        return $standing;
    }

    public function delete(Standing $standing): void
    {
        $this->em->remove($standing);
        $this->em->flush();
    }
}
