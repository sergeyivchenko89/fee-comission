<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Service;

class MathService
{
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function add(float $leftOperand, float $rightOperand): float
    {
        return (float) bcadd((string) $leftOperand, (string) $rightOperand, $this->scale);
    }

    public function sub(float $leftOperand, float $rightOperand): float
    {
        return (float) bcsub((string) $leftOperand, (string) $rightOperand, $this->scale);
    }
}
