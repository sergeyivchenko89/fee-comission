<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface CurrencyExchangerInterface
{
    function convert(float $amount, string $currency, bool $reverse = false): float;

    public function getBaseCurrency(): string;

    public function getBaseNoFee(): float;
}