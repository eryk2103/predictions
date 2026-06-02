<?php

namespace App\Tests\Service;

use App\Entity\Game;
use App\Entity\Prediction;
use App\Enum\GameStatusEnum;
use App\Repository\PredictionRepositoryInterface;
use App\Service\PredictionPointService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class PredictionPointServiceTest extends TestCase
{
    private PredictionRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private PredictionPointService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PredictionRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new PredictionPointService($this->repository, $this->em);
    }

    private function gameWithScore(int $home, int $away, ?int $homePenalty = null, ?int $awayPenalty = null): Game
    {
        return (new Game())
            ->setStatus(GameStatusEnum::FINISHED)
            ->setHomeScore($home)
            ->setAwayScore($away)
            ->setHomePenaltyScore($homePenalty)
            ->setAwayPenaltyScore($awayPenalty);
    }

    private function prediction(int $home, int $away, ?int $homePenalty = null, ?int $awayPenalty = null): Prediction
    {
        return (new Prediction())
            ->setHomeScore($home)
            ->setAwayScore($away)
            ->setHomePenalty($homePenalty)
            ->setAwayPenalty($awayPenalty);
    }

    #[Test]
    public function testSkipsCalculationWhenHomeScoreIsNull(): void
    {
        $game = (new Game())->setStatus(GameStatusEnum::IN_PROGRESS)->setAwayScore(1);

        $this->repository->expects($this->never())->method('findBy');
        $this->em->expects($this->never())->method('flush');

        $this->service->calculateForGame($game);
    }

    #[Test]
    public function testSkipsCalculationWhenAwayScoreIsNull(): void
    {
        $game = (new Game())->setStatus(GameStatusEnum::IN_PROGRESS)->setHomeScore(1);

        $this->repository->expects($this->never())->method('findBy');

        $this->service->calculateForGame($game);
    }

    #[Test]
    public function testExactScoreMatchGivesThreePoints(): void
    {
        $game = $this->gameWithScore(2, 1);
        $prediction = $this->prediction(2, 1);

        $this->repository->method('findBy')->willReturn([$prediction]);
        $this->em->expects($this->once())->method('flush');

        $this->service->calculateForGame($game);

        $this->assertSame(3, $prediction->getPoints());
    }

    #[Test]
    public function testCorrectOutcomeGivesOnePoint(): void
    {
        $game = $this->gameWithScore(3, 1);
        $prediction = $this->prediction(2, 0);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(1, $prediction->getPoints());
    }

    #[Test]
    public function testCorrectDrawOutcomeGivesOnePoint(): void
    {
        $game = $this->gameWithScore(1, 1);
        $prediction = $this->prediction(2, 2);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(1, $prediction->getPoints());
    }

    #[Test]
    public function testWrongOutcomeGivesZeroPoints(): void
    {
        $game = $this->gameWithScore(2, 0);
        $prediction = $this->prediction(0, 1);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(0, $prediction->getPoints());
    }

    #[Test]
    public function testExactDrawWithCorrectPenaltyWinnerGivesFourPoints(): void
    {
        $game = $this->gameWithScore(1, 1, 5, 3);
        $prediction = $this->prediction(1, 1, 4, 2);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(4, $prediction->getPoints());
    }

    #[Test]
    public function testExactDrawWithWrongPenaltyWinnerGivesThreePoints(): void
    {
        $game = $this->gameWithScore(1, 1, 3, 5);
        $prediction = $this->prediction(1, 1, 4, 2);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(3, $prediction->getPoints());
    }

    #[Test]
    public function testExactDrawWithNoPenaltyPredictionGivesThreePoints(): void
    {
        $game = $this->gameWithScore(0, 0, 4, 3);
        $prediction = $this->prediction(0, 0);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(3, $prediction->getPoints());
    }

    #[Test]
    public function testExactScoreWithNoPenaltyScoreInGameGivesThreePoints(): void
    {
        $game = $this->gameWithScore(1, 1);
        $prediction = $this->prediction(1, 1, 4, 3);

        $this->repository->method('findBy')->willReturn([$prediction]);

        $this->service->calculateForGame($game);

        $this->assertSame(3, $prediction->getPoints());
    }

    #[Test]
    public function testCalculatesPointsForMultiplePredictions(): void
    {
        $game = $this->gameWithScore(2, 0);
        $exact = $this->prediction(2, 0);
        $outcome = $this->prediction(3, 1);
        $wrong = $this->prediction(0, 1);

        $this->repository->method('findBy')->willReturn([$exact, $outcome, $wrong]);
        $this->em->expects($this->once())->method('flush');

        $this->service->calculateForGame($game);

        $this->assertSame(3, $exact->getPoints());
        $this->assertSame(1, $outcome->getPoints());
        $this->assertSame(0, $wrong->getPoints());
    }

    #[Test]
    public function testFlushesOnceForMultiplePredictions(): void
    {
        $game = $this->gameWithScore(1, 0);
        $predictions = array_fill(0, 5, $this->prediction(1, 0));

        $this->repository->method('findBy')->willReturn($predictions);
        $this->em->expects($this->once())->method('flush');

        $this->service->calculateForGame($game);
    }
}
