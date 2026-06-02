<?php

namespace App\Tests\Service;

use App\Dto\CreateTeamDto;
use App\Dto\UpdateTeamDto;
use App\Entity\Country;
use App\Entity\Stadium;
use App\Entity\Team;
use App\Exception\TeamNotFoundException;
use App\Repository\TeamRepositoryInterface;
use App\Service\TeamService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class TeamServiceTest extends TestCase
{
    private TeamRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private TeamService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TeamRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new TeamService($this->repository, $this->em);
    }

    #[Test]
    public function testCreatePersistsAndReturnsTeam(): void
    {
        $country = new Country();
        $stadium = new Stadium();
        $dto = new CreateTeamDto('Arsenal', 'ARS', 'ARS', 'arsenal.png', 'London', $country, $stadium);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Team::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($dto);

        $this->assertSame('Arsenal', $result->getName());
        $this->assertSame('ARS', $result->getShortName());
        $this->assertSame('ARS', $result->getCode());
        $this->assertSame('arsenal.png', $result->getLogo());
        $this->assertSame('London', $result->getCity());
        $this->assertSame($country, $result->getCountry());
        $this->assertSame($stadium, $result->getStadium());
    }

    #[Test]
    public function testCreateWithNullOptionals(): void
    {
        $dto = new CreateTeamDto('Chelsea', 'CHE', 'CHE', 'chelsea.png');

        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($dto);

        $this->assertNull($result->getCity());
        $this->assertNull($result->getCountry());
        $this->assertNull($result->getStadium());
    }

    #[Test]
    public function testGetReturnsTeam(): void
    {
        $team = new Team();
        $this->repository->expects($this->once())->method('find')->with(7)->willReturn($team);

        $this->assertSame($team, $this->service->get(7));
    }

    #[Test]
    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->expects($this->once())->method('find')->with(99)->willReturn(null);

        $this->expectException(TeamNotFoundException::class);
        $this->expectExceptionMessage('Team 99 not found.');

        $this->service->get(99);
    }

    #[Test]
    public function testGetAllDelegatesToRepository(): void
    {
        $paginator = $this->createMock(Paginator::class);
        $this->repository->expects($this->once())->method('findPaginated')->with(1, 20)->willReturn($paginator);

        $this->assertSame($paginator, $this->service->getAll(1, 20));
    }

    #[Test]
    public function testUpdateSetsFieldsAndFlushes(): void
    {
        $team = new Team();
        $country = new Country();
        $stadium = new Stadium();
        $dto = new UpdateTeamDto('Liverpool', 'LIV', 'LIV', 'lfc.png', 'Liverpool', $country, $stadium);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($team, $dto);

        $this->assertSame($team, $result);
        $this->assertSame('Liverpool', $team->getName());
        $this->assertSame('LIV', $team->getShortName());
        $this->assertSame('LIV', $team->getCode());
        $this->assertSame('lfc.png', $team->getLogo());
        $this->assertSame('Liverpool', $team->getCity());
        $this->assertSame($country, $team->getCountry());
        $this->assertSame($stadium, $team->getStadium());
    }

    #[Test]
    public function testDeleteRemovesAndFlushes(): void
    {
        $team = new Team();

        $this->em->expects($this->once())->method('remove')->with($team);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($team);
    }
}
