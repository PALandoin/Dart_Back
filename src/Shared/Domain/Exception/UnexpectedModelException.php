<?php

namespace App\Shared\Domain\Exception;

use App\Shared\Domain\Model\Model;

class UnexpectedModelException extends \DomainException
{
    public function __construct(Model $model, string $expectedModel)
    {
        parent::__construct(sprintf(
            'Unexpected model %s, expected %s',
            get_class($model),
            $expectedModel
        ), 422);
    }
}
