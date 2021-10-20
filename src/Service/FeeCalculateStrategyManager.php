<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service;

use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyManagerInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Exception\FeeCalculateStrategyNotFound;

class FeeCalculateStrategyManager implements FeeCalculateStrategyManagerInterface
{
    private $strategies;

    public function __construct()
    {
        $this->strategies = [];
    }

    public function addStrategy(FeeCalculateStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    public function getFee(OperationInterface $operation): float
    {
        /** @var FeeCalculateStrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->canApply($operation)) {
                return $strategy->getFee($operation);
            }
        }

        throw new FeeCalculateStrategyNotFound($operation);
    }
}