<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Game>
 */
interface GameRepositoryInterface extends ObjectRepository
{
    /** @return Game[] */
    public function findByCurrentPhases(): array;

    /** @return Game[] */
    public function findAllByCompetition(int $competitionId): array;

    public function findByFilters(?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): Paginator;
}
