<?php

namespace App;

use App\Entity\Competition;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompetitionService
{
    public function __construct(private readonly CompetitionRepository  $repository,
                                private readonly EntityManagerInterface $em)
    {
    }

    public function create(string $name, string $shortName, int $startYear, int $endYear, ?string $logo = null): Competition
    {
        $competition = (new Competition())
            ->setName($name)
            ->setShortName($shortName)
            ->setStartYear($startYear)
            ->setEndYear($endYear)
            ->setLogo($logo);

        $this->em->persist($competition);
        $this->em->flush();

        return $competition;
    }

    public function save(Competition $competition): Competition
    {
        $this->em->persist($competition);
        $this->em->flush();

        return $competition;
    }

    public function get(int $id): Competition
    {
        $competition = $this->repository->find($id);

        if ($competition === null) {
            throw new NotFoundHttpException(sprintf('Competition %d not found.', $id));
        }

        return $competition;
    }

    /** @return Competition[] */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function update(Competition $competition, ?string $name = null, ?string $shortName = null,
                           ?int $startYear = null, ?int $endYear = null, ?string $logo = null): Competition
    {
        if ($name !== null) {
            $competition->setName($name);
        }
        if ($shortName !== null) {
            $competition->setShortName($shortName);
        }
        if ($startYear !== null) {
            $competition->setStartYear($startYear);
        }
        if ($endYear !== null) {
            $competition->setEndYear($endYear);
        }
        if ($logo !== null) {
            $competition->setLogo($logo);
        }

        $this->em->flush();

        return $competition;
    }

    public function delete(Competition $competition): void
    {
        $this->em->remove($competition);
        $this->em->flush();
    }
}
