<?php

namespace App\Tests\Service;

use App\Repository\UserRepositoryInterface;
use App\Service\RankingService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RankingServiceTest extends TestCase
{
    private UserRepositoryInterface&MockObject $repository;
    private RankingService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new RankingService($this->repository);
    }

    public function testGetRankingsDelegatesToRepository(): void
    {
        $rankings = [
            ['id' => 1, 'email' => 'a@a.com', 'points' => 10],
            ['id' => 2, 'email' => 'b@b.com', 'points' => 5],
        ];

        $this->repository->expects($this->once())
            ->method('getRankings')
            ->with(null, null)
            ->willReturn($rankings);

        $this->assertSame($rankings, $this->service->getRankings());
    }

    public function testGetRankingsWithFilters(): void
    {
        $this->repository->expects($this->once())
            ->method('getRankings')
            ->with(3, 7)
            ->willReturn([]);

        $this->service->getRankings(3, 7);
    }
}
