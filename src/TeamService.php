<?php

namespace App;

use App\Entity\Country;
use App\Entity\Stadium;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamService
{
    public function __construct(
        private readonly TeamRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(
        string $name,
        string $shortName,
        string $code,
        string $logo,
        ?string $city = null,
        ?Country $country = null,
        ?Stadium $stadium = null,
    ): Team {
        $team = (new Team())
            ->setName($name)
            ->setShortName($shortName)
            ->setCode($code)
            ->setLogo($logo)
            ->setCity($city)
            ->setCountry($country)
            ->setStadium($stadium);

        $this->em->persist($team);
        $this->em->flush();

        return $team;
    }

    public function get(int $id): Team
    {
        $team = $this->repository->find($id);

        if ($team === null) {
            throw new NotFoundHttpException(sprintf('Team %d not found.', $id));
        }

        return $team;
    }

    public function getAll(int $page = 1, int $perPage = 20): Paginator
    {
        return $this->repository->findPaginated($page, $perPage);
    }

    public function update(
        Team $team,
        ?string $name = null,
        ?string $shortName = null,
        ?string $code = null,
        ?string $logo = null,
        ?string $city = null,
        ?Country $country = null,
        ?Stadium $stadium = null,
    ): Team {
        if ($name !== null) {
            $team->setName($name);
        }
        if ($shortName !== null) {
            $team->setShortName($shortName);
        }
        if ($code !== null) {
            $team->setCode($code);
        }
        if ($logo !== null) {
            $team->setLogo($logo);
        }
        if ($city !== null) {
            $team->setCity($city);
        }
        if ($country !== null) {
            $team->setCountry($country);
        }
        if ($stadium !== null) {
            $team->setStadium($stadium);
        }

        $this->em->flush();

        return $team;
    }

    public function delete(Team $team): void
    {
        $this->em->remove($team);
        $this->em->flush();
    }
}
