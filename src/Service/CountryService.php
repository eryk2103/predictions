<?php

namespace App\Service;

use App\Dto\CreateCountryDto;
use App\Dto\UpdateCountryDto;
use App\Entity\Country;
use App\Exception\CountryNotFoundException;
use App\Repository\CountryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CountryService implements CountryServiceInterface
{
    public function __construct(
        private readonly CountryRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(CreateCountryDto $dto): Country
    {
        $country = (new Country())
            ->setName($dto->name)
            ->setFlag($dto->flag);

        $this->em->persist($country);
        $this->em->flush();

        return $country;
    }

    public function get(int $id): Country
    {
        $country = $this->repository->find($id);

        if ($country === null) {
            throw new CountryNotFoundException($id);
        }

        return $country;
    }

    public function getAll(int $page = 1, int $perPage = 20): Paginator
    {
        return $this->repository->findPaginated($page, $perPage);
    }

    public function update(Country $country, UpdateCountryDto $dto): Country
    {
        $country
            ->setName($dto->name)
            ->setFlag($dto->flag);

        $this->em->flush();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->em->remove($country);
        $this->em->flush();
    }
}
