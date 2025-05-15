<?php

declare(strict_types=1);

namespace App\Scoring\Infrastructure\Doctrine\Repository;

use App\Scoring\Domain\Model\DartThrow;
use App\Scoring\Domain\Repository\DartThrowRepository as DartThrowRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DartThrow>
 *
 * @method DartThrow|null find($id, $lockMode = null, $lockVersion = null)
 */
class DartThrowRepository extends ServiceEntityRepository implements DartThrowRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DartThrow::class);
    }

    public function save(DartThrow $dartThrow): void
    {
        $this->getEntityManager()->persist($dartThrow);
        $this->getEntityManager()->flush();
    }
}
