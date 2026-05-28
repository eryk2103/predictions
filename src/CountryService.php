<?php

namespace App;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryService
{
    public function __construct(
        private readonly CountryRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(string $name, string $flag): Country
    {
        $country = (new Country())
            ->setName($name)
            ->setFlag($flag);

        $this->em->persist($country);
        $this->em->flush();

        return $country;
    }

    public function get(int $id): Country
    {
        $country = $this->repository->find($id);

        if ($country === null) {
            throw new NotFoundHttpException(sprintf('Country %d not found.', $id));
        }

        return $country;
    }

    /** @return Country[] */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function update(Country $country, ?string $name = null, ?string $flag = null): Country
    {
        if ($name !== null) {
            $country->setName($name);
        }
        if ($flag !== null) {
            $country->setFlag($flag);
        }

        $this->em->flush();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->em->remove($country);
        $this->em->flush();
    }
}
