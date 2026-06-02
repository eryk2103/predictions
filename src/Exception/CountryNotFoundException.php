<?php

namespace App\Exception;

class CountryNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Country %d not found.', $id));
    }
}
