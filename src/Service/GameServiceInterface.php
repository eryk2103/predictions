<?php

namespace App\Service;

use App\Dto\CreateGameDto;
use App\Dto\UpdateGameDto;
use App\Entity\Game;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface GameServiceInterface
{
    public function create(CreateGameDto $dto): Game;

    public function get(int $id): Game;

    public function getAll(?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): Paginator;

    /** @return array<int, Game[]> */
    public function getGroupedByPhase(int $competitionId): array;

    public function update(Game $game, UpdateGameDto $dto): Game;

    public function delete(Game $game): void;
}
