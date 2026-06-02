<?php

namespace App\Exception;

class CompetitionPhaseNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Competition phase %d not found.', $id));
    }
}
