<?php

namespace App;

use App\Repository\UserRepository;

class RankingService
{
    public function __construct(private readonly UserRepository $repository) {}

    /** @return array<int, array{id: int, email: string, points: int}> */
    public function getRankings(?int $competitionId = null, ?int $phaseId = null): array
    {
        return $this->repository->getRankings($competitionId, $phaseId);
    }
}
