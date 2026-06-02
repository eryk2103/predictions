<?php

namespace App\Tests\Service;

use App\Dto\CreatePredictionDto;
use App\Dto\UpdatePredictionDto;
use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;
use App\Exception\BusinessRuleException;
use App\Exception\DuplicatePredictionException;
use App\Exception\GameAlreadyStartedException;
use App\Exception\PredictionNotFoundException;
use App\Repository\PredictionRepositoryInterface;
use App\Service\PredictionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PredictionServiceTest extends TestCase
{
    private PredictionRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private PredictionService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PredictionRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new PredictionService($this->repository, $this->em);
    }

    private function futureGame(): Game
    {
        return (new Game())->setDatetime(new \DateTime('+1 day'));
    }

    public function testFindByUserAndGameDelegatesToRepository(): void
    {
        $user = new User();
        $game = new Game();
        $prediction = new Prediction();

        $this->repository->expects($this->once())
            ->method('findByUserAndGame')
            ->with($user, $game)
            ->willReturn($prediction);

        $this->assertSame($prediction, $this->service->findByUserAndGame($user, $game));
    }

    public function testCreatePersistsAndReturnsPrediction(): void
    {
        $user = new User();
        $game = $this->futureGame();
        $dto = new CreatePredictionDto(2, 1);

        $this->repository->method('findByUserAndGame')->willReturn(null);
        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Prediction::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($user, $game, $dto);

        $this->assertSame($user, $result->getPredictor());
        $this->assertSame($game, $result->getGame());
        $this->assertSame(2, $result->getHomeScore());
        $this->assertSame(1, $result->getAwayScore());
        $this->assertNull($result->getHomePenalty());
        $this->assertNull($result->getAwayPenalty());
    }

    public function testCreateWithPenalties(): void
    {
        $user = new User();
        $game = $this->futureGame();
        $dto = new CreatePredictionDto(1, 1, 4, 3);

        $this->repository->method('findByUserAndGame')->willReturn(null);
        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($user, $game, $dto);

        $this->assertSame(4, $result->getHomePenalty());
        $this->assertSame(3, $result->getAwayPenalty());
    }

    public function testCreateThrowsDuplicatePredictionException(): void
    {
        $user = new User();
        $game = $this->futureGame();

        $this->repository->method('findByUserAndGame')->willReturn(new Prediction());

        $this->expectException(DuplicatePredictionException::class);

        $this->service->create($user, $game, new CreatePredictionDto(1, 0));
    }

    public function testCreateThrowsGameAlreadyStartedException(): void
    {
        $user = new User();
        $game = (new Game())->setDatetime(new \DateTime('-1 hour'));

        $this->repository->method('findByUserAndGame')->willReturn(null);

        $this->expectException(GameAlreadyStartedException::class);

        $this->service->create($user, $game, new CreatePredictionDto(1, 0));
    }

    public function testCreateAllowsGameWithNoDatetime(): void
    {
        $user = new User();
        $game = new Game();

        $this->repository->method('findByUserAndGame')->willReturn(null);
        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($user, $game, new CreatePredictionDto(0, 0));

        $this->assertInstanceOf(Prediction::class, $result);
    }

    public function testCreateExceptionsExtendBusinessRuleException(): void
    {
        $this->assertInstanceOf(BusinessRuleException::class, new DuplicatePredictionException());
        $this->assertInstanceOf(BusinessRuleException::class, new GameAlreadyStartedException());
    }

    public function testGetReturnsPrediction(): void
    {
        $prediction = new Prediction();
        $this->repository->expects($this->once())->method('find')->with(7)->willReturn($prediction);

        $this->assertSame($prediction, $this->service->get(7));
    }

    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->method('find')->with(99)->willReturn(null);

        $this->expectException(PredictionNotFoundException::class);
        $this->expectExceptionMessage('Prediction 99 not found.');

        $this->service->get(99);
    }

    public function testGetByUserDelegatesToRepository(): void
    {
        $user = new User();
        $predictions = [new Prediction()];

        $this->repository->expects($this->once())
            ->method('findByUserFiltered')
            ->with($user, 1, 2, 1, 20)
            ->willReturn($predictions);

        $this->assertSame($predictions, $this->service->getByUser($user, 1, 2, 1, 20));
    }

    public function testCountByUserDelegatesToRepository(): void
    {
        $user = new User();

        $this->repository->expects($this->once())
            ->method('countByUserFiltered')
            ->with($user, null, null)
            ->willReturn(5);

        $this->assertSame(5, $this->service->countByUser($user));
    }

    public function testSumPointsByUserDelegatesToRepository(): void
    {
        $user = new User();

        $this->repository->expects($this->once())
            ->method('sumPointsByUser')
            ->with($user, null, null)
            ->willReturn(42);

        $this->assertSame(42, $this->service->sumPointsByUser($user));
    }

    public function testUpdateSetsFieldsAndFlushes(): void
    {
        $prediction = (new Prediction())
            ->setHomeScore(1)
            ->setAwayScore(0)
            ->setGame($this->futureGame());

        $dto = new UpdatePredictionDto(2, 2, 5, 3);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($prediction, $dto);

        $this->assertSame($prediction, $result);
        $this->assertSame(2, $prediction->getHomeScore());
        $this->assertSame(2, $prediction->getAwayScore());
        $this->assertSame(5, $prediction->getHomePenalty());
        $this->assertSame(3, $prediction->getAwayPenalty());
    }

    public function testUpdateThrowsGameAlreadyStartedException(): void
    {
        $prediction = (new Prediction())
            ->setGame((new Game())->setDatetime(new \DateTime('-1 hour')));

        $this->expectException(GameAlreadyStartedException::class);

        $this->service->update($prediction, new UpdatePredictionDto(1, 0));
    }

    public function testDeleteRemovesAndFlushes(): void
    {
        $prediction = new Prediction();

        $this->em->expects($this->once())->method('remove')->with($prediction);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($prediction);
    }
}
