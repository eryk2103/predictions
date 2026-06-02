<?php

namespace App\Tests\Service;

use App\Dto\CreateStandingDto;
use App\Dto\UpdateStandingDto;
use App\Entity\Competition;
use App\Entity\Standing;
use App\Entity\Team;
use App\Exception\StandingNotFoundException;
use App\Repository\StandingRepositoryInterface;
use App\Service\StandingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class StandingServiceTest extends TestCase
{
    private StandingRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private StandingService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(StandingRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new StandingService($this->repository, $this->em);
    }

    #[Test]
    public function testCreatePersistsAndReturnsStanding(): void
    {
        $competition = new Competition();
        $team = new Team();
        $dto = new CreateStandingDto($competition, $team, 1, 10, 7, 2, 1, 20, 8, 23);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Standing::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($dto);

        $this->assertSame($competition, $result->getCompetition());
        $this->assertSame($team, $result->getTeam());
        $this->assertSame(1, $result->getPosition());
        $this->assertSame(10, $result->getPlayed());
        $this->assertSame(7, $result->getWon());
        $this->assertSame(2, $result->getDrawn());
        $this->assertSame(1, $result->getLost());
        $this->assertSame(20, $result->getGoals());
        $this->assertSame(8, $result->getGoalsAgainst());
        $this->assertSame(23, $result->getPoints());
    }

    #[Test]
    public function testGetReturnsStanding(): void
    {
        $standing = new Standing();
        $this->repository->expects($this->once())->method('find')->with(5)->willReturn($standing);

        $this->assertSame($standing, $this->service->get(5));
    }

    #[Test]
    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->expects($this->once())->method('find')->with(99)->willReturn(null);

        $this->expectException(StandingNotFoundException::class);
        $this->expectExceptionMessage('Standing 99 not found.');

        $this->service->get(99);
    }

    #[Test]
    public function testGetAllDelegatesToRepository(): void
    {
        $standings = [new Standing()];
        $this->repository->expects($this->once())->method('findByCompetition')->with(3)->willReturn($standings);

        $this->assertSame($standings, $this->service->getAll(3));
    }

    #[Test]
    public function testGetAllWithNoFilterDelegatesToRepository(): void
    {
        $this->repository->expects($this->once())->method('findByCompetition')->with(null)->willReturn([]);

        $this->service->getAll();
    }

    #[Test]
    public function testUpdateSetsFieldsAndFlushes(): void
    {
        $standing = new Standing();
        $competition = new Competition();
        $team = new Team();
        $dto = new UpdateStandingDto($competition, $team, 2, 8, 5, 1, 2, 15, 10, 16);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($standing, $dto);

        $this->assertSame($standing, $result);
        $this->assertSame($competition, $standing->getCompetition());
        $this->assertSame($team, $standing->getTeam());
        $this->assertSame(2, $standing->getPosition());
        $this->assertSame(8, $standing->getPlayed());
        $this->assertSame(5, $standing->getWon());
        $this->assertSame(1, $standing->getDrawn());
        $this->assertSame(2, $standing->getLost());
        $this->assertSame(15, $standing->getGoals());
        $this->assertSame(10, $standing->getGoalsAgainst());
        $this->assertSame(16, $standing->getPoints());
    }

    #[Test]
    public function testDeleteRemovesAndFlushes(): void
    {
        $standing = new Standing();

        $this->em->expects($this->once())->method('remove')->with($standing);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($standing);
    }
}
