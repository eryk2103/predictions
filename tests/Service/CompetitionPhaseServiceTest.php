<?php

namespace App\Tests\Service;

use App\Dto\CreateCompetitionPhaseDto;
use App\Dto\UpdateCompetitionPhaseDto;
use App\Entity\Competition;
use App\Entity\CompetitionPhase;
use App\Enum\PhaseTypeEnum;
use App\Exception\CompetitionPhaseNotFoundException;
use App\Repository\CompetitionPhaseRepositoryInterface;
use App\Service\CompetitionPhaseService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CompetitionPhaseServiceTest extends TestCase
{
    private CompetitionPhaseRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private CompetitionPhaseService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CompetitionPhaseRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new CompetitionPhaseService($this->repository, $this->em);
    }

    public function testCreatePersistsAndReturnsPhase(): void
    {
        $competition = new Competition();
        $dto = new CreateCompetitionPhaseDto('Group Stage', PhaseTypeEnum::GROUP, 1);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(CompetitionPhase::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($competition, $dto);

        $this->assertSame($competition, $result->getCompetition());
        $this->assertSame('Group Stage', $result->getName());
        $this->assertSame(PhaseTypeEnum::GROUP, $result->getType());
        $this->assertSame(1, $result->getSequence());
        $this->assertFalse($result->isCurrent());
    }

    public function testCreateWithIsCurrentTrue(): void
    {
        $competition = new Competition();
        $dto = new CreateCompetitionPhaseDto('Knockout', PhaseTypeEnum::KNOCKOUT, 2, true);

        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($competition, $dto);

        $this->assertTrue($result->isCurrent());
    }

    public function testGetReturnsPhase(): void
    {
        $phase = new CompetitionPhase();
        $this->repository->expects($this->once())->method('find')->with(42)->willReturn($phase);

        $this->assertSame($phase, $this->service->get(42));
    }

    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->method('find')->with(99)->willReturn(null);

        $this->expectException(CompetitionPhaseNotFoundException::class);
        $this->expectExceptionMessage('Competition phase 99 not found.');

        $this->service->get(99);
    }

    public function testUpdateSetsAllFieldsAndFlushes(): void
    {
        $phase = (new CompetitionPhase())
            ->setName('Old')
            ->setType(PhaseTypeEnum::GROUP)
            ->setSequence(1)
            ->setIsCurrent(false);

        $dto = new UpdateCompetitionPhaseDto('New', PhaseTypeEnum::KNOCKOUT, 2, true);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($phase, $dto);

        $this->assertSame($phase, $result);
        $this->assertSame('New', $phase->getName());
        $this->assertSame(PhaseTypeEnum::KNOCKOUT, $phase->getType());
        $this->assertSame(2, $phase->getSequence());
        $this->assertTrue($phase->isCurrent());
    }

    public function testDeleteRemovesAndFlushes(): void
    {
        $phase = new CompetitionPhase();

        $this->em->expects($this->once())->method('remove')->with($phase);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($phase);
    }
}
