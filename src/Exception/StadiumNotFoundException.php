<?php

namespace App\Exception;

class StadiumNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Stadium %d not found.', $id));
    }
}
