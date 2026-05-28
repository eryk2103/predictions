<?php

namespace App;

use App\Entity\CompetitionPhase;
use App\Entity\Game;
use App\Entity\Stadium;
use App\Entity\Team;
use App\Enum\GameStatusEnum;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameService
{
    public function __construct(
        private readonly GameRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(
        CompetitionPhase $competitionPhase,
        GameStatusEnum $status,
        ?Team $homeTeam = null,
        ?Team $awayTeam = null,
        ?\DateTime $datetime = null,
        ?Stadium $stadium = null,
        ?int $homeScore = null,
        ?int $awayScore = null,
        ?int $homePenaltyScore = null,
        ?int $awayPenaltyScore = null,
    ): Game {
        $game = (new Game())
            ->setCompetitionPhase($competitionPhase)
            ->setStatus($status)
            ->setHomeTeam($homeTeam)
            ->setAwayTeam($awayTeam)
            ->setDatetime($datetime)
            ->setStadium($stadium)
            ->setHomeScore($homeScore)
            ->setAwayScore($awayScore)
            ->setHomePenaltyScore($homePenaltyScore)
            ->setAwayPenaltyScore($awayPenaltyScore);

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }

    public function get(int $id): Game
    {
        $game = $this->repository->find($id);

        if ($game === null) {
            throw new NotFoundHttpException(sprintf('Game %d not found.', $id));
        }

        return $game;
    }

    public function getAll(?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): Paginator
    {
        return $this->repository->findByFilters($competitionId, $phaseId, $page, $perPage);
    }

    public function update(
        Game $game,
        ?CompetitionPhase $competitionPhase = null,
        ?GameStatusEnum $status = null,
        ?Team $homeTeam = null,
        ?Team $awayTeam = null,
        ?\DateTime $datetime = null,
        ?Stadium $stadium = null,
        ?int $homeScore = null,
        ?int $awayScore = null,
        ?int $homePenaltyScore = null,
        ?int $awayPenaltyScore = null,
    ): Game {
        if ($competitionPhase !== null) {
            $game->setCompetitionPhase($competitionPhase);
        }
        if ($status !== null) {
            $game->setStatus($status);
        }
        if ($homeTeam !== null) {
            $game->setHomeTeam($homeTeam);
        }
        if ($awayTeam !== null) {
            $game->setAwayTeam($awayTeam);
        }
        if ($datetime !== null) {
            $game->setDatetime($datetime);
        }
        if ($stadium !== null) {
            $game->setStadium($stadium);
        }
        if ($homeScore !== null) {
            $game->setHomeScore($homeScore);
        }
        if ($awayScore !== null) {
            $game->setAwayScore($awayScore);
        }
        if ($homePenaltyScore !== null) {
            $game->setHomePenaltyScore($homePenaltyScore);
        }
        if ($awayPenaltyScore !== null) {
            $game->setAwayPenaltyScore($awayPenaltyScore);
        }

        $this->em->flush();

        return $game;
    }

    public function delete(Game $game): void
    {
        $this->em->remove($game);
        $this->em->flush();
    }
}
