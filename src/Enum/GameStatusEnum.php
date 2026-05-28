<?php

namespace App\Enum;

enum GameStatusEnum: string
{
    case TO_BE_CONFIRMED = 'to_be_confirmed';
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case HALF_TIME = 'half_time';
    case FINISHED = 'finished';
    case EXTRA_TIME = 'extra_time';
    case PENALTIES = 'penalties';
}
