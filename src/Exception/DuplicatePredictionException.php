<?php

namespace App\Exception;

class DuplicatePredictionException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct('User already has a prediction for this game.');
    }
}
