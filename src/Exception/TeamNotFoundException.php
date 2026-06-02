<?php

namespace App\Exception;

class TeamNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Team %d not found.', $id));
    }
}
