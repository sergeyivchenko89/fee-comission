<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface FeeCalculateStrategyInterface
{
    public const WITHDRAW = 'WITHDRAW';
    public const DEPOSIT = 'DEPOSIT';
    public const PRIVATE = 'PRIVATE';
    public const BUSINESS = 'BUSINESS';

    public function canApply(OperationInterface $operation): bool;

    public function getFee(OperationInterface $operation): string;
}