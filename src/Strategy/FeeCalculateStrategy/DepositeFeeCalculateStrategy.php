<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class DepositeFeeCalculateStrategy extends AbstractFeeCalculateStrategy
{
    public function canApply(OperationInterface $operation): bool
    {
        return 'deposit' === $operation->getOperationType();
    }

    protected function getAmount2Fee(OperationInterface $operation): float
    {
        return $operation->getAmount();
    }
}