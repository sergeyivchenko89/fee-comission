<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Service;

use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;

class MathService implements MathServiceInterface
{
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function add(float $leftOperand, float $rightOperand): float
    {
        return (float)bcadd((string)$leftOperand, (string)$rightOperand, $this->scale);
    }

    public function sub(float $leftOperand, float $rightOperand): float
    {
        return (float)bcsub((string)$leftOperand, (string)$rightOperand, $this->scale);
    }

    public function mul(float $leftOperand, float $rightOperand): float
    {
        $result = bcmul((string)$leftOperand, (string)$rightOperand, 10);
        return (float)bcmul($result, '1', $this->scale);
    }

    public function div(float $leftOperand, float $rightOperand): float
    {
        $result = bcdiv((string)$leftOperand, (string)$rightOperand, 10);
        return (float)bcmul($result, '1', $this->scale);
    }

    public function comp(float $leftOperand, float $rightOperand): int
    {
        return bccomp((string) $leftOperand, (string) $rightOperand, 10);
    }
}
