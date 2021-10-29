<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class DepositFeeCalculateStrategy extends AbstractFeeCalculateStrategy
{
    public function canApply(OperationInterface $operation): bool
    {
        return strtolower(self::DEPOSIT) === strtolower($operation->getOperationType());
    }

    protected function getAmount2Fee(OperationInterface $operation): string
    {
        return $operation->getAmount();
    }
}