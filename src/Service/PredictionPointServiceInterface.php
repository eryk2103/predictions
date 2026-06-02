<?php

namespace App\Service;

use App\Entity\Game;

interface PredictionPointServiceInterface
{
    public function calculateForGame(Game $game): void;
}
