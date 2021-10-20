<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

abstract class AbstractFeeCalculateStrategy implements FeeCalculateStrategyInterface
{
    protected $feeValue;

    public function __construct(float $feeValue)
    {
        $this->feeValue = $feeValue;
    }

    public function getFee(OperationInterface $operation): float
    {
        $result = bcmul((string) $this->getAmount2Fee($operation), (string) $this->feeValue, 4);
        return (float) bcmul($result, '1', 2);
    }

    abstract protected function getAmount2Fee(OperationInterface $operation): float;
}