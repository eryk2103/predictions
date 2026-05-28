<?php

namespace App;

use App\Entity\Country;
use App\Entity\Stadium;
use App\Repository\StadiumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StadiumService
{
    public function __construct(
        private readonly StadiumRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(string $name, string $city, ?int $capacity, ?Country $country): Stadium
    {
        $stadium = (new Stadium())
            ->setName($name)
            ->setCity($city)
            ->setCapacity($capacity)
            ->setCountry($country);

        $this->em->persist($stadium);
        $this->em->flush();

        return $stadium;
    }

    public function get(int $id): Stadium
    {
        $stadium = $this->repository->find($id);

        if ($stadium === null) {
            throw new NotFoundHttpException(sprintf('Stadium %d not found.', $id));
        }

        return $stadium;
    }

    /** @return Stadium[] */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function update(
        Stadium $stadium,
        ?string $name = null,
        ?string $city = null,
        ?int $capacity = null,
        ?Country $country = null,
    ): Stadium {
        if ($name !== null) {
            $stadium->setName($name);
        }
        if ($city !== null) {
            $stadium->setCity($city);
        }
        if ($capacity !== null) {
            $stadium->setCapacity($capacity);
        }
        if ($country !== null) {
            $stadium->setCountry($country);
        }

        $this->em->flush();

        return $stadium;
    }

    public function delete(Stadium $stadium): void
    {
        $this->em->remove($stadium);
        $this->em->flush();
    }
}
