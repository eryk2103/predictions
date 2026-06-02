<?php

namespace App\Exception;

class CompetitionNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Competition %d not found.', $id));
    }
}
