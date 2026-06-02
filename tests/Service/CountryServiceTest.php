<?php

namespace App\Tests\Service;

use App\Dto\CreateCountryDto;
use App\Dto\UpdateCountryDto;
use App\Entity\Country;
use App\Exception\CountryNotFoundException;
use App\Repository\CountryRepositoryInterface;
use App\Service\CountryService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class CountryServiceTest extends TestCase
{
    private CountryRepositoryInterface&MockObject $repository;
    private EntityManagerInterface&MockObject $em;
    private CountryService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CountryRepositoryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new CountryService($this->repository, $this->em);
    }

    #[Test]
    public function testCreatePersistsAndReturnsCountry(): void
    {
        $dto = new CreateCountryDto('Poland', 'pl.png');

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Country::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->create($dto);

        $this->assertSame('Poland', $result->getName());
        $this->assertSame('pl.png', $result->getFlag());
    }

    #[Test]
    public function testGetReturnsCountry(): void
    {
        $country = new Country();
        $this->repository->expects($this->once())->method('find')->with(1)->willReturn($country);

        $this->assertSame($country, $this->service->get(1));
    }

    #[Test]
    public function testGetThrowsNotFoundExceptionWhenMissing(): void
    {
        $this->repository->expects($this->once())->method('find')->with(5)->willReturn(null);

        $this->expectException(CountryNotFoundException::class);
        $this->expectExceptionMessage('Country 5 not found.');

        $this->service->get(5);
    }

    #[Test]
    public function testGetAllDelegatesToRepository(): void
    {
        $paginator = $this->createMock(Paginator::class);
        $this->repository->expects($this->once())->method('findPaginated')->with(2, 20)->willReturn($paginator);

        $this->assertSame($paginator, $this->service->getAll(2, 20));
    }

    #[Test]
    public function testUpdateSetsFieldsAndFlushes(): void
    {
        $country = new Country();
        $dto = new UpdateCountryDto('Germany', 'de.png');

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->update($country, $dto);

        $this->assertSame($country, $result);
        $this->assertSame('Germany', $country->getName());
        $this->assertSame('de.png', $country->getFlag());
    }

    #[Test]
    public function testDeleteRemovesAndFlushes(): void
    {
        $country = new Country();

        $this->em->expects($this->once())->method('remove')->with($country);
        $this->em->expects($this->once())->method('flush');

        $this->service->delete($country);
    }
}
