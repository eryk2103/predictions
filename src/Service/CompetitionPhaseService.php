<?php

namespace App\Service;

use App\Dto\CreateCompetitionPhaseDto;
use App\Dto\UpdateCompetitionPhaseDto;
use App\Entity\Competition;
use App\Entity\CompetitionPhase;
use App\Exception\CompetitionPhaseNotFoundException;
use App\Repository\CompetitionPhaseRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class CompetitionPhaseService implements CompetitionPhaseServiceInterface
{
    public function __construct(
        private readonly CompetitionPhaseRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(Competition $competition, CreateCompetitionPhaseDto $dto): CompetitionPhase
    {
        $phase = (new CompetitionPhase())
            ->setCompetition($competition)
            ->setName($dto->name)
            ->setType($dto->type)
            ->setSequence($dto->sequence)
            ->setIsCurrent($dto->isCurrent);

        $this->em->persist($phase);
        $this->em->flush();

        return $phase;
    }

    public function get(int $id): CompetitionPhase
    {
        $phase = $this->repository->find($id);

        if ($phase === null) {
            throw new CompetitionPhaseNotFoundException($id);
        }

        return $phase;
    }

    public function update(CompetitionPhase $phase, UpdateCompetitionPhaseDto $dto): CompetitionPhase
    {
        $phase->setName($dto->name)
            ->setType($dto->type)
            ->setSequence($dto->sequence)
            ->setIsCurrent($dto->isCurrent);

        $this->em->flush();

        return $phase;
    }

    public function delete(CompetitionPhase $phase): void
    {
        $this->em->remove($phase);
        $this->em->flush();
    }
}
