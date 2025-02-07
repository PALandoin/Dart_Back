<?php

namespace App\Shared\Domain\Bus\Query;

use App\Shared\Domain\Model\Model;

interface Response
{
    public static function fromModel(Model $model): static;
}
