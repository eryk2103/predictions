<?php

namespace App\Service;

use App\Dto\CreateCountryDto;
use App\Dto\UpdateCountryDto;
use App\Entity\Country;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface CountryServiceInterface
{
    public function create(CreateCountryDto $dto): Country;

    public function get(int $id): Country;

    public function getAll(int $page = 1, int $perPage = 20): Paginator;

    public function update(Country $country, UpdateCountryDto $dto): Country;

    public function delete(Country $country): void;
}
