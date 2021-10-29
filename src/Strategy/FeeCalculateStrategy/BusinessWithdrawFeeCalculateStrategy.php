<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class BusinessWithdrawFeeCalculateStrategy extends AbstractFeeCalculateStrategy
{
    public function canApply(OperationInterface $operation): bool
    {
        return self::WITHDRAW === $operation->getOperationType()
            && self::BUSINESS === $operation->getClientType();
    }

    protected function getAmount2Fee(OperationInterface $operation): string
    {
        return $operation->getAmount();
    }
}