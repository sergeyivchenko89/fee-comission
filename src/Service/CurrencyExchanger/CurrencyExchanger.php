<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Service\CurrencyExchanger;

use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Exception\CurrencyExchangerException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyExchanger extends AbstractCurrencyExchanger
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    private $rates = [];

    public function __construct(
        MathServiceInterface $mathService,
        HttpClientInterface $httpClient,
        string $baseCurrency,
        float $baseNoFee
    ) {
        parent::__construct($mathService, $baseCurrency, $baseNoFee);
        $this->httpClient = $httpClient;
    }

    public function convert(float $amount, string $currency, bool $reverse = false): float
    {
        if ($currency === $this->getBaseCurrency()) {
            return $amount;
        }

        if (empty($this->rates)) {
            $response = $this->httpClient->request('GET', 'latest')->toArray();

            if (true === ($response['success'] ?? false)) {
                $this->rates = $response['rates'];
            } elseif (isset($response['success'])) {
                throw new CurrencyExchangerException($response['error']['info']);
            } else {
                throw new CurrencyExchangerException('Error retrieving data from the currency service.');
            }
        }

        if (empty($this->rates)) {
            throw new CurrencyExchangerException(
                sprintf('Cannot find currency rate for %s and %s.', $currency, $this->getBaseCurrency())
            );
        }

        return call_user_func_array([$this->mathService, $reverse ? 'div' : 'mul'], [$amount, $this->rates[$currency]]);
    }
}