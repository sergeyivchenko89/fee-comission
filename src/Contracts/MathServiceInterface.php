<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Contracts;

interface MathServiceInterface
{
    public function add(string $leftOperand, string $rightOperand): string;

    public function sub(string $leftOperand, string $rightOperand): string;

    public function mul(string $leftOperand, string $rightOperand): string;

    public function div(string $leftOperand, string $rightOperand): string;

    public function comp(string $leftOperand, string $rightOperand): int;
}