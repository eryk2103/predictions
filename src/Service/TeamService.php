<?php

namespace App\Service;

use App\Dto\CreateTeamDto;
use App\Dto\UpdateTeamDto;
use App\Entity\Team;
use App\Exception\TeamNotFoundException;
use App\Repository\TeamRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TeamService implements TeamServiceInterface
{
    public function __construct(
        private readonly TeamRepositoryInterface $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(CreateTeamDto $dto): Team
    {
        $team = (new Team())
            ->setName($dto->name)
            ->setShortName($dto->shortName)
            ->setCode($dto->code)
            ->setLogo($dto->logo ?? '')
            ->setCity($dto->city)
            ->setCountry($dto->country)
            ->setStadium($dto->stadium);

        $this->em->persist($team);
        $this->em->flush();

        return $team;
    }

    public function get(int $id): Team
    {
        $team = $this->repository->find($id);

        if ($team === null) {
            throw new TeamNotFoundException($id);
        }

        return $team;
    }

    public function getAll(int $page = 1, int $perPage = 20): Paginator
    {
        return $this->repository->findPaginated($page, $perPage);
    }

    public function update(Team $team, UpdateTeamDto $dto): Team
    {
        $team
            ->setName($dto->name)
            ->setShortName($dto->shortName)
            ->setCode($dto->code)
            ->setCity($dto->city)
            ->setCountry($dto->country)
            ->setStadium($dto->stadium);

        if($dto->logo !== null){
            $team->setLogo($dto->logo);
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
