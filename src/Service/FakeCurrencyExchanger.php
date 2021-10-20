<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service;

use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Exception\IncorrectCurrency;

class FakeCurrencyExchanger implements CurrencyExchangerInterface
{
    public const EUR = 'eur';
    public const USD = 'usd';
    public const JPY = 'jpy';

    private const ACCURACY = 4;

    private $baseCurrency;

    private $baseNoFee;

    public function __construct(string $baseCurrency, float $baseNoFee)
    {
        $this->baseCurrency = strtolower($baseCurrency);
        $this->baseNoFee = $baseNoFee;
    }

    function convert(float $amount, string $currency, bool $reverse = false): float
    {
        $inputCurrency = strtolower($reverse ? $this->baseCurrency : $currency);
        $outputCurrency = strtolower($reverse ? $currency : $this->baseCurrency);

        if (self::JPY === $inputCurrency && self::EUR === $outputCurrency) {
            $rate = (float) bcdiv((string) 1, (string) 129.53, self::ACCURACY);
        } elseif (self::USD === $inputCurrency && self::EUR === $outputCurrency) {
            $rate = (float) bcdiv((string) 1, (string) 1.1497, self::ACCURACY);
        } elseif (self::EUR === $inputCurrency && self::EUR === $outputCurrency) {
            $rate = 1;
        } elseif (self::JPY === $outputCurrency && self::EUR === $inputCurrency) {
            $rate = 129.53;
        } elseif (self::USD === $outputCurrency && self::EUR === $inputCurrency) {
            $rate = 1.1497;
        } else {
            throw new IncorrectCurrency("Некорректно указана одна из валют: $inputCurrency или $outputCurrency.");
        }

        $result = bcmul((string) $amount, (string) $rate, 4);
        return (float) bcmul($result, '1', 2);
    }

    public function getBaseNoFee(): float
    {
        return 1 === bccomp((string) $this->baseNoFee, '0', 2) ? $this->baseNoFee : 0;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }
}