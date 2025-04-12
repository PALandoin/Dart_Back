<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Query;

use App\Shared\Domain\Model\Model;

interface Response
{
    public static function fromModel(Model $model): self;
}
