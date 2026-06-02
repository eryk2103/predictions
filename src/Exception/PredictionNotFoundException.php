<?php

namespace App\Exception;

class PredictionNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Prediction %d not found.', $id));
    }
}
