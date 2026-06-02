<?php

namespace App\Exception;

class StandingNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Standing %d not found.', $id));
    }
}
