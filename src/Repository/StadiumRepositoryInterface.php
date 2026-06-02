<?php

namespace App\Repository;

use App\Entity\Stadium;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Stadium>
 */
interface StadiumRepositoryInterface extends ObjectRepository
{
    public function findPaginated(int $page, int $perPage): Paginator;
}
