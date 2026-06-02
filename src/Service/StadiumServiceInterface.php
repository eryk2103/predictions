<?php

namespace App\Service;

use App\Dto\CreateStadiumDto;
use App\Dto\UpdateStadiumDto;
use App\Entity\Stadium;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface StadiumServiceInterface
{
    public function create(CreateStadiumDto $dto): Stadium;

    public function get(int $id): Stadium;

    public function getAll(int $page = 1, int $perPage = 20): Paginator;

    public function update(Stadium $stadium, UpdateStadiumDto $dto): Stadium;

    public function delete(Stadium $stadium): void;
}
