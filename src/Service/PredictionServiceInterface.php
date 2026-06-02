<?php

namespace App\Service;

use App\Dto\CreatePredictionDto;
use App\Dto\UpdatePredictionDto;
use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;

interface PredictionServiceInterface
{
    public function findByUserAndGame(User $user, Game $game): ?Prediction;

    public function create(User $predictor, Game $game, CreatePredictionDto $dto): Prediction;

    public function get(int $id): Prediction;

    /** @return Prediction[] */
    public function getByUser(User $user, ?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): array;

    public function countByUser(User $user, ?int $competitionId = null, ?int $phaseId = null): int;

    public function sumPointsByUser(User $user, ?int $competitionId = null, ?int $phaseId = null): int;

    public function update(Prediction $prediction, UpdatePredictionDto $dto): Prediction;

    public function delete(Prediction $prediction): void;
}
