<?php

declare(strict_types=1);

namespace App\Scoring\Infrastructure\Doctrine\Repository;

use App\Scoring\Domain\Model\Scoring;
use App\Scoring\Domain\Repository\ScoringRepository as ScoringRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scoring>
 *
 * @method Scoring|null find($id, $lockMode = null, $lockVersion = null)
 */
class ScoringRepository extends ServiceEntityRepository implements ScoringRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scoring::class);
    }

    public function save(Scoring $scoring): void
    {
        $this->getEntityManager()->persist($scoring);
        $this->getEntityManager()->flush();
    }
}
