<?php

namespace App\Exception;

class GameAlreadyStartedException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct('Predictions are not allowed after the game has started.');
    }
}
