<?php

namespace App\Service;

use App\Dto\CreateStandingDto;
use App\Dto\UpdateStandingDto;
use App\Entity\Standing;

interface StandingServiceInterface
{
    public function create(CreateStandingDto $dto): Standing;

    public function get(int $id): Standing;

    /** @return Standing[] */
    public function getAll(?int $competitionId = null): array;

    public function update(Standing $standing, UpdateStandingDto $dto): Standing;

    public function delete(Standing $standing): void;
}
