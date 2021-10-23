<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service\CurrencyExchanger;

use SergeiIvchenko\CommissionTask\Exception\IncorrectCurrency;

class FakeCurrencyExchanger extends AbstractCurrencyExchanger
{
    function convert(float $amount, string $currency, bool $reverse = false): float
    {
        $inputCurrency = strtolower($reverse ? $this->getBaseCurrency() : $currency);
        $outputCurrency = strtolower($reverse ? $currency : $this->getBaseCurrency());

        if (self::JPY === $inputCurrency && self::EUR === $outputCurrency) {
            $rate = (float)bcdiv((string)1, (string)129.53, self::ACCURACY);
        } elseif (self::USD === $inputCurrency && self::EUR === $outputCurrency) {
            $rate = (float)bcdiv((string)1, (string)1.1497, self::ACCURACY);
        } elseif (self::EUR === $inputCurrency && self::EUR === $outputCurrency) {
            $rate = 1;
        } elseif (self::JPY === $outputCurrency && self::EUR === $inputCurrency) {
            $rate = 129.53;
        } elseif (self::USD === $outputCurrency && self::EUR === $inputCurrency) {
            $rate = 1.1497;
        } else {
            throw new IncorrectCurrency("Некорректно указана одна из валют: $inputCurrency или $outputCurrency.");
        }

        $result = bcmul((string)$amount, (string)$rate, 4);
        return (float)bcmul($result, '1', 2);
    }
}