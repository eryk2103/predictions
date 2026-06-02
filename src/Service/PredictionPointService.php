<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Prediction;
use App\Enum\GameStatusEnum;
use App\Repository\PredictionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PredictionPointService implements PredictionPointServiceInterface
{
    public function __construct(
        private readonly PredictionRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function calculateForGame(Game $game): void
    {
        if ($game->getHomeScore() === null || $game->getAwayScore() === null) {
            return;
        }

        $predictions = $this->repository->findBy(['game' => $game]);

        foreach ($predictions as $prediction) {
            $prediction->setPoints($this->calculate($prediction, $game));
        }

        $this->em->flush();
    }

    private function calculate(Prediction $prediction, Game $game): int
    {
        $actualHome = $game->getHomeScore();
        $actualAway = $game->getAwayScore();
        $predHome = $prediction->getHomeScore();
        $predAway = $prediction->getAwayScore();

        if ($predHome === $actualHome && $predAway === $actualAway) {
            $points = 3;

            if ($actualHome === $actualAway && $game->getHomePenaltyScore() !== null) {
                $points += $this->calculatePenaltyBonus($prediction, $game);
            }

            return $points;
        }

        if ($this->getOutcome($predHome, $predAway) === $this->getOutcome($actualHome, $actualAway)) {
            return 1;
        }

        return 0;
    }

    private function calculatePenaltyBonus(Prediction $prediction, Game $game): int
    {
        if ($prediction->getHomePenalty() === null || $prediction->getAwayPenalty() === null) {
            return 0;
        }

        $predictedWinner = $prediction->getHomePenalty() <=> $prediction->getAwayPenalty();
        $actualWinner = $game->getHomePenaltyScore() <=> $game->getAwayPenaltyScore();

        return $predictedWinner === $actualWinner ? 1 : 0;
    }

    private function getOutcome(int $home, int $away): int
    {
        return $home <=> $away;
    }
}
