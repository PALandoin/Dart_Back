<?php

declare(strict_types=1);

namespace App\Scoring\Domain\Repository;

use App\Scoring\Domain\Model\Scoring;

/**
 * @method Scoring|null find($id, $lockMode = null, $lockVersion = null)
 */
interface ScoringRepository
{
    public function save(Scoring $scoring): void;
}
