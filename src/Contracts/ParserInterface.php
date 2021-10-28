<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Contracts;

interface ParserInterface
{
    public function getOperation(string $stringData): OperationInterface;
}