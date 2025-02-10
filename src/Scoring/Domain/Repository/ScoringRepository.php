<?php

namespace App\Scoring\Domain\Repository;

use App\Scoring\Domain\Model\Scoring;

interface ScoringRepository
{
    public function save(Scoring $scoring): void;
}
