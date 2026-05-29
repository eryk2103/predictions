<?php

namespace App\Service;

use App\Entity\Competition;
use App\Entity\Standing;
use App\Entity\Team;
use App\Repository\StandingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StandingService
{
    public function __construct(
        private readonly StandingRepository     $repository,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function create(
        Competition $competition,
        Team        $team,
        int         $position,
        int         $played,
        int         $won,
        int         $drawn,
        int         $lost,
        int         $goals,
        int         $goalsAgainst,
        int         $points,
    ): Standing
    {
        $standing = (new Standing())
            ->setCompetition($competition)
            ->setTeam($team)
            ->setPosition($position)
            ->setPlayed($played)
            ->setWon($won)
            ->setDrawn($drawn)
            ->setLost($lost)
            ->setGoals($goals)
            ->setGoalsAgainst($goalsAgainst)
            ->setPoints($points);

        $this->em->persist($standing);
        $this->em->flush();

        return $standing;
    }

    public function get(int $id): Standing
    {
        $standing = $this->repository->find($id);

        if ($standing === null) {
            throw new NotFoundHttpException(sprintf('Standing %d not found.', $id));
        }

        return $standing;
    }

    /** @return Standing[] */
    public function getAll(?int $competitionId = null): array
    {
        return $this->repository->findByCompetition($competitionId);
    }

    public function update(
        Standing    $standing,
        Competition $competition,
        Team        $team,
        int         $position,
        int         $played,
        int         $won,
        int         $drawn,
        int         $lost,
        int         $goals,
        int         $goalsAgainst,
        int         $points,
    ): Standing
    {
        $standing
            ->setCompetition($competition)
            ->setTeam($team)
            ->setPosition($position)
            ->setPlayed($played)
            ->setWon($won)
            ->setDrawn($drawn)
            ->setLost($lost)
            ->setGoals($goals)
            ->setGoalsAgainst($goalsAgainst)
            ->setPoints($points);

        $this->em->flush();

        return $standing;
    }

    public function delete(Standing $standing): void
    {
        $this->em->remove($standing);
        $this->em->flush();
    }
}
