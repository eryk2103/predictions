<?php

namespace App\Tests\Service;

use App\Dto\CreateCompetitionDto;
use App\Dto\UpdateCompetitionDto;
use App\Entity\Competition;
use App\Exception\CompetitionNotFoundException;
use App\Repository\CompetitionRepositoryInterface;
use App\Service\CompetitionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class CompetitionServiceTest extends TestCase
{
    private CompetitionRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private CompetitionService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CompetitionRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new CompetitionService($this->repository, $this->em);
    }

    #[Test]
    public function testCreatePersistsAndReturnsCompetition(): void
    {
        $dto = new CreateCompetitionDto('Premier League', 'PL', 2024, 2025, 'logo.png');

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Competition::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($dto);

        $this->assertSame('Premier League', $result->getName());
        $this->assertSame('PL', $result->getShortName());
        $this->assertSame(2024, $result->getStartYear());
        $this->assertSame(2025, $result->getEndYear());
        $this->assertSame('logo.png', $result->getLogo());
    }

    #[Test]
    public function testCreateWithNullLogo(): void
    {
        $dto = new CreateCompetitionDto('La Liga', 'LL', 2024, 2025);

        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($dto);

        $this->assertNull($result->getLogo());
    }

    #[Test]
    public function testGetReturnsCompetition(): void
    {
        $competition = new Competition();
        $this->repository->expects($this->once())->method('find')->with(1)->willReturn($competition);

        $this->assertSame($competition, $this->service->get(1));
    }

    #[Test]
    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->expects($this->once())->method('find')->with(99)->willReturn(null);

        $this->expectException(CompetitionNotFoundException::class);
        $this->expectExceptionMessage('Competition 99 not found.');

        $this->service->get(99);
    }

    #[Test]
    public function testGetAllDelegatesToRepository(): void
    {
        $competitions = [new Competition(), new Competition()];
        $this->repository->expects($this->once())->method('findAll')->willReturn($competitions);

        $this->assertSame($competitions, $this->service->getAll());
    }

    #[Test]
    public function testUpdateSetsAllFieldsAndFlushes(): void
    {
        $competition = new Competition();
        $dto = new UpdateCompetitionDto('Updated', 'UP', 2023, 2024, 'new.png');

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($competition, $dto);

        $this->assertSame($competition, $result);
        $this->assertSame('Updated', $competition->getName());
        $this->assertSame('UP', $competition->getShortName());
        $this->assertSame(2023, $competition->getStartYear());
        $this->assertSame(2024, $competition->getEndYear());
        $this->assertSame('new.png', $competition->getLogo());
    }

    #[Test]
    public function testDeleteRemovesAndFlushes(): void
    {
        $competition = new Competition();

        $this->em->expects($this->once())->method('remove')->with($competition);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($competition);
    }
}
