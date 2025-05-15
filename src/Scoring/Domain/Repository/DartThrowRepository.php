<?php

declare(strict_types=1);

namespace App\Scoring\Domain\Repository;

use App\Scoring\Domain\Model\DartThrow;

interface DartThrowRepository
{
    public function save(DartThrow $dartThrow): void;
}
