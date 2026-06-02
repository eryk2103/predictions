<?php

namespace App\Service;

use App\Dto\CreateGameDto;
use App\Dto\UpdateGameDto;
use App\Entity\Game;
use App\Exception\GameNotFoundException;
use App\Repository\GameRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class GameService implements GameServiceInterface
{
    public function __construct(
        private readonly GameRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(CreateGameDto $dto): Game
    {
        $game = (new Game())
            ->setCompetitionPhase($dto->competitionPhase)
            ->setStatus($dto->status)
            ->setHomeTeam($dto->homeTeam)
            ->setAwayTeam($dto->awayTeam)
            ->setDatetime($dto->datetime)
            ->setStadium($dto->stadium)
            ->setHomeScore($dto->homeScore)
            ->setAwayScore($dto->awayScore)
            ->setHomePenaltyScore($dto->homePenaltyScore)
            ->setAwayPenaltyScore($dto->awayPenaltyScore);

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }

    public function get(int $id): Game
    {
        $game = $this->repository->find($id);

        if ($game === null) {
            throw new GameNotFoundException($id);
        }

        return $game;
    }

    public function getAll(?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): Paginator
    {
        return $this->repository->findByFilters($competitionId, $phaseId, $page, $perPage);
    }

    /** @return array<int, Game[]> games indexed by phase id */
    public function getGroupedByPhase(int $competitionId): array
    {
        $grouped = [];
        foreach ($this->repository->findAllByCompetition($competitionId) as $game) {
            $grouped[$game->getCompetitionPhase()->getId()][] = $game;
        }

        return $grouped;
    }

    public function update(Game $game, UpdateGameDto $dto): Game
    {
        $game
            ->setCompetitionPhase($dto->competitionPhase)
            ->setStatus($dto->status)
            ->setHomeTeam($dto->homeTeam)
            ->setAwayTeam($dto->awayTeam)
            ->setDatetime($dto->datetime)
            ->setStadium($dto->stadium)
            ->setHomeScore($dto->homeScore)
            ->setAwayScore($dto->awayScore)
            ->setHomePenaltyScore($dto->homePenaltyScore)
            ->setAwayPenaltyScore($dto->awayPenaltyScore);

        $this->em->flush();

        return $game;
    }

    public function delete(Game $game): void
    {
        $this->em->remove($game);
        $this->em->flush();
    }
}
