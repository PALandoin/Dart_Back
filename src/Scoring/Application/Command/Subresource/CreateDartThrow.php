<?php

namespace App\Scoring\Application\Command\Subresource;

use App\Scoring\Domain\Model\Enum\MultiplicationEnum;
use App\Scoring\Domain\Model\Enum\SectionEnum;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDartThrow
{
    public function __construct(
        #[Assert\Choice(callback: [MultiplicationEnum::class, 'getValues'])]
        public int $multiplication,
        #[Assert\Choice(callback: [SectionEnum::class, 'getValues'])]
        public int $section,
    ) {
    }
}
