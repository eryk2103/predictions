<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Country>
 */
interface CountryRepositoryInterface extends ObjectRepository
{
    public function findPaginated(int $page, int $perPage): Paginator;
}
