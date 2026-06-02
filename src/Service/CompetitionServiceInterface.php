<?php

namespace App\Service;

use App\Dto\CreateCompetitionDto;
use App\Dto\UpdateCompetitionDto;
use App\Entity\Competition;

interface CompetitionServiceInterface
{
    public function create(CreateCompetitionDto $dto): Competition;

    public function get(int $id): Competition;

    /** @return Competition[] */
    public function getAll(): array;

    public function update(Competition $competition, UpdateCompetitionDto $dto): Competition;

    public function delete(Competition $competition): void;
}
