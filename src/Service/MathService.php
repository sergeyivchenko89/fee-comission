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

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public function sub(string $leftOperand, string $rightOperand): string
    {
        return bcsub($leftOperand, $rightOperand, $this->scale);
    }

    public function mul(string $leftOperand, string $rightOperand): string
    {
        $result = bcmul((string)$leftOperand, (string)$rightOperand, 10);
        return bcmul($result, '1', $this->scale);
    }

    public function div(string $leftOperand, string $rightOperand): string
    {
        $result = bcdiv((string)$leftOperand, (string)$rightOperand, 10);
        return bcmul($result, '1', $this->scale);
    }

    public function comp(string $leftOperand, string $rightOperand): int
    {
        return bccomp($leftOperand, $rightOperand, 10);
    }
}
