<?php

namespace App;

use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;
use App\Repository\PredictionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PredictionService
{
    public function __construct(
        private readonly PredictionRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function findByUserAndGame(User $user, Game $game): ?Prediction
    {
        return $this->repository->findByUserAndGame($user, $game);
    }

    public function create(
        User $predictor,
        Game $game,
        int $homeScore,
        int $awayScore,
        ?int $homePenalty = null,
        ?int $awayPenalty = null,
    ): Prediction {
        if ($this->repository->findByUserAndGame($predictor, $game) !== null) {
            throw new \LogicException('User already has a prediction for this game.');
        }

        $prediction = (new Prediction())
            ->setPredictor($predictor)
            ->setGame($game)
            ->setHomeScore($homeScore)
            ->setAwayScore($awayScore)
            ->setHomePenalty($homePenalty)
            ->setAwayPenalty($awayPenalty);

        $this->em->persist($prediction);
        $this->em->flush();

        return $prediction;
    }

    public function get(int $id): Prediction
    {
        $prediction = $this->repository->find($id);

        if ($prediction === null) {
            throw new NotFoundHttpException(sprintf('Prediction %d not found.', $id));
        }

        return $prediction;
    }

    /** @return Prediction[] */
    public function getByUser(User $user, int $page = 1, int $perPage = 20): array
    {
        return $this->repository->findBy(
            ['predictor' => $user],
            ['id' => 'DESC'],
            $perPage,
            ($page - 1) * $perPage,
        );
    }

    public function countByUser(User $user): int
    {
        return $this->repository->count(['predictor' => $user]);
    }

    public function update(
        Prediction $prediction,
        ?int $homeScore = null,
        ?int $awayScore = null,
        ?int $homePenalty = null,
        ?int $awayPenalty = null,
    ): Prediction {
        if ($homeScore !== null) {
            $prediction->setHomeScore($homeScore);
        }
        if ($awayScore !== null) {
            $prediction->setAwayScore($awayScore);
        }
        $prediction->setHomePenalty($homePenalty);
        $prediction->setAwayPenalty($awayPenalty);

        $this->em->flush();

        return $prediction;
    }

    public function delete(Prediction $prediction): void
    {
        $this->em->remove($prediction);
        $this->em->flush();
    }
}
