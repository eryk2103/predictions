<?php

namespace App\Service;

use App\Repository\UserRepositoryInterface;

class RankingService implements RankingServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $repository) {}

    /** @return array<int, array{id: int, email: string, points: int}> */
    public function getRankings(?int $competitionId = null, ?int $phaseId = null): array
    {
        return $this->repository->getRankings($competitionId, $phaseId);
    }
}
