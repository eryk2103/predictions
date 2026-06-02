<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Team>
 */
interface TeamRepositoryInterface extends ObjectRepository
{
    public function findPaginated(int $page, int $perPage): Paginator;
}
