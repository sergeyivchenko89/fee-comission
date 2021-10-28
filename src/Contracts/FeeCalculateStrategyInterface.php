<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface FeeCalculateStrategyInterface
{
    public const WITHDRAW = 'withdraw';
    public const DEPOSIT = 'deposit';
    public const PRIVATE = 'private';
    public const BUSINESS = 'business';

    public function canApply(OperationInterface $operation): bool;

    public function getFee(OperationInterface $operation): float;
}