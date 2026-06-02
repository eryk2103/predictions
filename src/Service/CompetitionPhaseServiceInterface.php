<?php

namespace App\Service;

use App\Dto\CreateCompetitionPhaseDto;
use App\Dto\UpdateCompetitionPhaseDto;
use App\Entity\Competition;
use App\Entity\CompetitionPhase;

interface CompetitionPhaseServiceInterface
{
    public function create(Competition $competition, CreateCompetitionPhaseDto $dto): CompetitionPhase;

    public function get(int $id): CompetitionPhase;

    public function update(CompetitionPhase $phase, UpdateCompetitionPhaseDto $dto): CompetitionPhase;

    public function delete(CompetitionPhase $phase): void;
}
