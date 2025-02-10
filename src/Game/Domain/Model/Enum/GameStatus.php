<?php

namespace App\Game\Domain\Model\Enum;

enum GameStatus: string
{
    case CREATED = 'Created';
    case STARTED = 'Started';
    case FINISHED = 'Finished';
}
