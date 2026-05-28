<?php

namespace App;

use App\Entity\Competition;
use App\Entity\CompetitionPhase;
use App\Enum\PhaseTypeEnum;
use App\Repository\CompetitionPhaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompetitionPhaseService
{
    public function __construct(
        private readonly CompetitionPhaseRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(Competition $competition, string $name, PhaseTypeEnum $type, int $sequence): CompetitionPhase
    {
        $phase = (new CompetitionPhase())
            ->setCompetition($competition)
            ->setName($name)
            ->setType($type)
            ->setSequence($sequence);

        $this->em->persist($phase);
        $this->em->flush();

        return $phase;
    }

    public function get(int $id): CompetitionPhase
    {
        $phase = $this->repository->find($id);

        if ($phase === null) {
            throw new NotFoundHttpException(sprintf('Competition phase %d not found.', $id));
        }

        return $phase;
    }

    public function update(CompetitionPhase $phase, ?string $name = null, ?PhaseTypeEnum $type = null, ?int $sequence = null): CompetitionPhase
    {
        if ($name !== null) {
            $phase->setName($name);
        }
        if ($type !== null) {
            $phase->setType($type);
        }
        if ($sequence !== null) {
            $phase->setSequence($sequence);
        }

        $this->em->flush();

        return $phase;
    }

    public function delete(CompetitionPhase $phase): void
    {
        $this->em->remove($phase);
        $this->em->flush();
    }
}
