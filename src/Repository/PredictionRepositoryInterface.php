<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Prediction>
 */
interface PredictionRepositoryInterface extends ObjectRepository
{
    public function findByUserAndGame(User $user, Game $game): ?Prediction;

    /** @return Prediction[] */
    public function findByUserFiltered(User $user, ?int $competitionId, ?int $phaseId, int $page, int $perPage): array;

    public function countByUserFiltered(User $user, ?int $competitionId, ?int $phaseId): int;

    public function sumPointsByUser(User $user, ?int $competitionId, ?int $phaseId): int;

    /**
     * @param Game[] $games
     * @return array<int, Prediction>
     */
    public function findByUserForGames(User $user, array $games): array;
}
