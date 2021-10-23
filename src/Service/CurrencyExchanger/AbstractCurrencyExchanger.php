<?php

namespace SergeiIvchenko\CommissionTask\Service\CurrencyExchanger;

use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;

abstract class AbstractCurrencyExchanger implements CurrencyExchangerInterface
{
    private $baseCurrency;

    private $baseNoFee;

    public function __construct(string $baseCurrency, float $baseNoFee)
    {
        $this->baseCurrency = strtolower($baseCurrency);
        $this->baseNoFee = $baseNoFee;
    }

    public function getBaseNoFee(): float
    {
        return 1 === bccomp((string)$this->baseNoFee, '0', 2) ? $this->baseNoFee : 0;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }
}