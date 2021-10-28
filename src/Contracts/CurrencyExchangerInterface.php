<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface CurrencyExchangerInterface
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';

    public const ACCURACY = 4;

    public function convert(float $amount, string $currency, bool $reverse = false): float;

    public function getBaseCurrency(): string;

    public function getBaseNoFee(): float;
}