<?php

namespace App\Tests\Service;

use App\Dto\CreateStadiumDto;
use App\Dto\UpdateStadiumDto;
use App\Entity\Country;
use App\Entity\Stadium;
use App\Exception\StadiumNotFoundException;
use App\Repository\StadiumRepositoryInterface;
use App\Service\StadiumService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StadiumServiceTest extends TestCase
{
    private StadiumRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private StadiumService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(StadiumRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new StadiumService($this->repository, $this->em);
    }

    public function testCreatePersistsAndReturnsStadium(): void
    {
        $country = new Country();
        $dto = new CreateStadiumDto('Wembley', 'London', 90000, $country);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Stadium::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($dto);

        $this->assertSame('Wembley', $result->getName());
        $this->assertSame('London', $result->getCity());
        $this->assertSame(90000, $result->getCapacity());
        $this->assertSame($country, $result->getCountry());
    }

    public function testCreateWithNullOptionals(): void
    {
        $dto = new CreateStadiumDto('Arena', 'Warsaw');

        $this->em->method('persist');
        $this->em->method('flush');

        $result = $this->service->create($dto);

        $this->assertNull($result->getCapacity());
        $this->assertNull($result->getCountry());
    }

    public function testGetReturnsStadium(): void
    {
        $stadium = new Stadium();
        $this->repository->expects($this->once())->method('find')->with(3)->willReturn($stadium);

        $this->assertSame($stadium, $this->service->get(3));
    }

    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->method('find')->with(99)->willReturn(null);

        $this->expectException(StadiumNotFoundException::class);
        $this->expectExceptionMessage('Stadium 99 not found.');

        $this->service->get(99);
    }

    public function testGetAllDelegatesToRepository(): void
    {
        $paginator = $this->createMock(Paginator::class);
        $this->repository->expects($this->once())->method('findPaginated')->with(1, 20)->willReturn($paginator);

        $this->assertSame($paginator, $this->service->getAll(1, 20));
    }

    public function testUpdateSetsFieldsAndFlushes(): void
    {
        $stadium = new Stadium();
        $country = new Country();
        $dto = new UpdateStadiumDto('Updated Arena', 'Krakow', 50000, $country);

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($stadium, $dto);

        $this->assertSame($stadium, $result);
        $this->assertSame('Updated Arena', $stadium->getName());
        $this->assertSame('Krakow', $stadium->getCity());
        $this->assertSame(50000, $stadium->getCapacity());
        $this->assertSame($country, $stadium->getCountry());
    }

    public function testDeleteRemovesAndFlushes(): void
    {
        $stadium = new Stadium();

        $this->em->expects($this->once())->method('remove')->with($stadium);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($stadium);
    }
}
