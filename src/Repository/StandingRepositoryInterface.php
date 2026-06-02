<?php

namespace App\Repository;

use App\Entity\Standing;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Standing>
 */
interface StandingRepositoryInterface extends ObjectRepository
{
    /** @return Standing[] */
    public function findByCompetition(?int $competitionId = null): array;
}
