<?php

namespace App\Service;

use App\Dto\CreateTeamDto;
use App\Dto\UpdateTeamDto;
use App\Entity\Team;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface TeamServiceInterface
{
    public function create(CreateTeamDto $dto): Team;

    public function get(int $id): Team;

    public function getAll(int $page = 1, int $perPage = 20): Paginator;

    public function update(Team $team, UpdateTeamDto $dto): Team;

    public function delete(Team $team): void;
}
