<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case POSTPONED = 'postponed';
    case CANCELED = 'canceled';
}