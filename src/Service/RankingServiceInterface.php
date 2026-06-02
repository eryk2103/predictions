<?php

namespace App\Service;

interface RankingServiceInterface
{
    /** @return array<int, array{id: int, email: string, points: int}> */
    public function getRankings(?int $competitionId = null, ?int $phaseId = null): array;
}
