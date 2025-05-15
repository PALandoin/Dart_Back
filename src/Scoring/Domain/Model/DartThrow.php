<?php

declare(strict_types=1);

namespace App\Scoring\Domain\Model;

use App\Scoring\Domain\Model\Enum\MultiplicationEnum;
use App\Scoring\Domain\Model\Enum\SectionEnum;
use App\Shared\Domain\Model\Model;
use InvalidArgumentException;

class DartThrow implements Model
{
    /**
     * @phpstan-ignore property.onlyRead
     */
    private int $id;

    private MultiplicationEnum $multiplication;

    private SectionEnum $section;

    private Scoring $scoring;

    public static function create(int $multiplication, int $section, Scoring $scoring): self
    {
        $dartThrow = new self();
        $dartThrow->multiplication = MultiplicationEnum::from($multiplication);
        $dartThrow->section = SectionEnum::from($section);
        $dartThrow->scoring = $scoring;

        if ($dartThrow->multiplication === MultiplicationEnum::TRIPLE && $dartThrow->section === SectionEnum::BULLSEYE) {
            throw new InvalidArgumentException('Invalid dart throw');
        }

        return $dartThrow;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMultplication(): MultiplicationEnum
    {
        return $this->multiplication;
    }

    public function setMultiplication(MultiplicationEnum $multiplication): self
    {
        $this->multiplication = $multiplication;

        return $this;
    }

    public function getSection(): SectionEnum
    {
        return $this->section;
    }

    public function setSection(SectionEnum $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getScore(): int
    {
        return $this->multiplication->value * $this->section->value;
    }

    public function getScoring(): Scoring
    {
        return $this->scoring;
    }

    public function setScoring(Scoring $scoring): self
    {
        $this->scoring = $scoring;

        return $this;
    }
}
