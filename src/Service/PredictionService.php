<?php

namespace App\Service;

use App\Dto\CreatePredictionDto;
use App\Dto\UpdatePredictionDto;
use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;
use App\Exception\DuplicatePredictionException;
use App\Exception\GameAlreadyStartedException;
use App\Exception\PredictionNotFoundException;
use App\Repository\PredictionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PredictionService implements PredictionServiceInterface
{
    public function __construct(
        private readonly PredictionRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function findByUserAndGame(User $user, Game $game): ?Prediction
    {
        return $this->repository->findByUserAndGame($user, $game);
    }

    private function assertGameNotStarted(Game $game): void
    {
        if ($game->getDatetime() !== null && $game->getDatetime() <= new \DateTime()) {
            throw new GameAlreadyStartedException();
        }
    }

    public function create(User $predictor, Game $game, CreatePredictionDto $dto): Prediction
    {
        if ($this->repository->findByUserAndGame($predictor, $game) !== null) {
            throw new DuplicatePredictionException();
        }

        $this->assertGameNotStarted($game);

        $prediction = (new Prediction())
            ->setPredictor($predictor)
            ->setGame($game)
            ->setHomeScore($dto->homeScore)
            ->setAwayScore($dto->awayScore)
            ->setHomePenalty($dto->homePenalty)
            ->setAwayPenalty($dto->awayPenalty);

        $this->em->persist($prediction);
        $this->em->flush();

        return $prediction;
    }

    public function get(int $id): Prediction
    {
        $prediction = $this->repository->find($id);

        if ($prediction === null) {
            throw new PredictionNotFoundException($id);
        }

        return $prediction;
    }

    /** @return Prediction[] */
    public function getByUser(User $user, ?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): array
    {
        return $this->repository->findByUserFiltered($user, $competitionId, $phaseId, $page, $perPage);
    }

    public function countByUser(User $user, ?int $competitionId = null, ?int $phaseId = null): int
    {
        return $this->repository->countByUserFiltered($user, $competitionId, $phaseId);
    }

    public function sumPointsByUser(User $user, ?int $competitionId = null, ?int $phaseId = null): int
    {
        return $this->repository->sumPointsByUser($user, $competitionId, $phaseId);
    }

    public function update(Prediction $prediction, UpdatePredictionDto $dto): Prediction
    {
        $this->assertGameNotStarted($prediction->getGame());

        $prediction
            ->setHomeScore($dto->homeScore)
            ->setAwayScore($dto->awayScore)
            ->setHomePenalty($dto->homePenalty)
            ->setAwayPenalty($dto->awayPenalty);

        $this->em->flush();

        return $prediction;
    }

    public function delete(Prediction $prediction): void
    {
        $this->em->remove($prediction);
        $this->em->flush();
    }
}
