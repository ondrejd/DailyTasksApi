<?php

namespace App\Enums;

/**
 * @OA\Schema()
 */
enum TaskStatusEnum: string
{
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case POSTPONED = 'postponed';
    case CANCELED = 'canceled';
}