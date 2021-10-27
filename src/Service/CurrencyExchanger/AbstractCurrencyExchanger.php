<?php

namespace SergeiIvchenko\CommissionTask\Service\CurrencyExchanger;

use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;

abstract class AbstractCurrencyExchanger implements CurrencyExchangerInterface
{
    /**
     * @var MathServiceInterface
     */
    protected $mathService;

    protected $baseCurrency;

    protected $baseNoFee;

    public function __construct(MathServiceInterface $mathService, string $baseCurrency, float $baseNoFee)
    {
        $this->mathService = $mathService;
        $this->baseCurrency = strtolower($baseCurrency);
        $this->baseNoFee = $baseNoFee;
    }

    public function getBaseNoFee(): float
    {
        return 1 === $this->mathService->comp($this->baseNoFee, 0) ? $this->baseNoFee : 0;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }
}