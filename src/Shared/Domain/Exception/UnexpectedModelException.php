<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use App\Shared\Domain\Model\Model;
use DomainException;

use function sprintf;

class UnexpectedModelException extends DomainException
{
    public function __construct(Model $model, string $expectedModel)
    {
        parent::__construct(sprintf(
            'Unexpected model %s, expected %s',
            $model::class,
            $expectedModel,
        ), 422);
    }
}
