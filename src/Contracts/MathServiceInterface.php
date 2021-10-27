<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Contracts;

interface MathServiceInterface
{
    public function add(float $leftOperand, float $rightOperand): float;

    public function sub(float $leftOperand, float $rightOperand): float;

    public function mul(float $leftOperand, float $rightOperand): float;

    public function div(float $leftOperand, float $rightOperand): float;

    public function comp(float $leftOperand, float $rightOperand): int;
}