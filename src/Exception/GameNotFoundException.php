<?php

namespace App\Exception;

class GameNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Game %d not found.', $id));
    }
}
