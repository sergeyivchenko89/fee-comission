<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

abstract class AbstractFeeCalculateStrategy implements FeeCalculateStrategyInterface
{
    protected $feeValue;

    protected $mathService;

    public function __construct(MathServiceInterface $mathService, string $feeValue)
    {
        $this->mathService = $mathService;
        $this->feeValue = $feeValue;
    }

    public function getFee(OperationInterface $operation): string
    {
        return $this->mathService->mul($this->getAmount2Fee($operation), $this->feeValue);
    }

    abstract protected function getAmount2Fee(OperationInterface $operation): string;
}