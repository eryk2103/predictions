<?php

namespace App\Tests\Service;

use App\Dto\CreateGameDto;
use App\Dto\UpdateGameDto;
use App\Entity\CompetitionPhase;
use App\Entity\Game;
use App\Entity\Stadium;
use App\Entity\Team;
use App\Enum\GameStatusEnum;
use App\Exception\GameNotFoundException;
use App\Repository\GameRepositoryInterface;
use App\Service\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private GameRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private GameService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new GameService($this->repository, $this->em);
    }

    public function testCreatePersistsAndReturnsGame(): void
    {
        $phase = new CompetitionPhase();
        $homeTeam = new Team();
        $awayTeam = new Team();
        $stadium = new Stadium();
        $datetime = new \DateTime('2024-06-01 15:00');

        $dto = new CreateGameDto($phase, GameStatusEnum::SCHEDULED, $homeTeam, $awayTeam, $datetime, $stadium, 0, 0);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Game::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($dto);

        $this->assertSame($phase, $result->getCompetitionPhase());
        $this->assertSame(GameStatusEnum::SCHEDULED, $result->getStatus());
        $this->assertSame($homeTeam, $result->getHomeTeam());
        $this->assertSame($awayTeam, $result->getAwayTeam());
        $this->assertSame($datetime, $result->getDatetime());
        $this->assertSame($stadium, $result->getStadium());
        $this->assertSame(0, $result->getHomeScore());
        $this->assertSame(0, $result->getAwayScore());
    }

    public function testCreateWithNullOptionals(): void
    {
        $phase = new CompetitionPhase();
        $dto = new CreateGameDto($phase, GameStatusEnum::SCHEDULED);

        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($dto);

        $this->assertNull($result->getHomeTeam());
        $this->assertNull($result->getAwayTeam());
        $this->assertNull($result->getDatetime());
        $this->assertNull($result->getStadium());
        $this->assertNull($result->getHomeScore());
        $this->assertNull($result->getAwayScore());
    }

    public function testGetReturnsGame(): void
    {
        $game = new Game();
        $this->repository->expects($this->once())->method('find')->with(10)->willReturn($game);

        $this->assertSame($game, $this->service->get(10));
    }

    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->method('find')->with(99)->willReturn(null);

        $this->expectException(GameNotFoundException::class);
        $this->expectExceptionMessage('Game 99 not found.');

        $this->service->get(99);
    }

    public function testGetAllDelegatesToRepository(): void
    {
        $paginator = $this->createMock(Paginator::class);
        $this->repository->expects($this->once())
            ->method('findByFilters')
            ->with(1, 2, 1, 20)
            ->willReturn($paginator);

        $this->assertSame($paginator, $this->service->getAll(1, 2, 1, 20));
    }

    public function testGetGroupedByPhaseGroupsByPhaseId(): void
    {
        $phase1 = new CompetitionPhase();
        $phase2 = new CompetitionPhase();

        // Use reflection to set IDs since there are no setters
        $setId = static function (CompetitionPhase $phase, int $id): void {
            $ref = new \ReflectionProperty($phase, 'id');
            $ref->setValue($phase, $id);
        };
        $setId($phase1, 1);
        $setId($phase2, 2);

        $game1 = (new Game())->setCompetitionPhase($phase1)->setStatus(GameStatusEnum::SCHEDULED);
        $game2 = (new Game())->setCompetitionPhase($phase1)->setStatus(GameStatusEnum::SCHEDULED);
        $game3 = (new Game())->setCompetitionPhase($phase2)->setStatus(GameStatusEnum::SCHEDULED);

        $this->repository->method('findAllByCompetition')->with(5)->willReturn([$game1, $game2, $game3]);

        $result = $this->service->getGroupedByPhase(5);

        $this->assertCount(2, $result);
        $this->assertCount(2, $result[1]);
        $this->assertCount(1, $result[2]);
    }

    public function testUpdateSetsFieldsAndFlushes(): void
    {
        $game = new Game();
        $phase = new CompetitionPhase();
        $dto = new UpdateGameDto($phase, GameStatusEnum::FINISHED, homeScore: 2, awayScore: 1);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($game, $dto);

        $this->assertSame($game, $result);
        $this->assertSame($phase, $game->getCompetitionPhase());
        $this->assertSame(GameStatusEnum::FINISHED, $game->getStatus());
        $this->assertSame(2, $game->getHomeScore());
        $this->assertSame(1, $game->getAwayScore());
    }

    public function testDeleteRemovesAndFlushes(): void
    {
        $game = new Game();

        $this->em->expects($this->once())->method('remove')->with($game);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($game);
    }
}
