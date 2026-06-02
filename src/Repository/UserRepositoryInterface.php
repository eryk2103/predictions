<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ObjectRepository<User>
 */
interface UserRepositoryInterface extends ObjectRepository, PasswordUpgraderInterface
{
    /** @return array<int, array{id: int, email: string, points: int}> */
    public function getRankings(?int $competitionId = null, ?int $phaseId = null): array;
}
