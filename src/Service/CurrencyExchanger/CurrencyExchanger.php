<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Service\CurrencyExchanger;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CurrencyExchanger extends AbstractCurrencyExchanger
{
    private $cache;

    private $apiUrl;

    private $apiVersion;

    private $apiKey;

    public function __construct(
        CacheInterface $cache,
        string         $baseCurrency,
        float          $baseNoFee,
        string         $apiUrl,
        string         $apiVersion,
        string         $apiKey
    )
    {
        parent::__construct($baseCurrency, $baseNoFee);

        $this->cache = $cache;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->apiVersion = $apiVersion;
    }

    function convert(float $amount, string $currency, bool $reverse = false): float
    {
        $key = $currency . '|' . $this->getBaseCurrency();
        $value = (string)$this->cache->get($key, function (ItemInterface $item) use ($currency) {

            if ($currency === $this->getBaseCurrency()) {
                return 1;
            }

            /* закешируем на час. */
            $item->expiresAfter(3600);

            /* Строим ендпоинт для запроса. */
            $currency = strtoupper($currency);
            $requestUrl = implode('/', [
                    $this->apiUrl,
                    $this->apiVersion
                ]) . '?' .
                implode('&', [
                    'access_key=' . $this->apiKey,
                    'symbols=' . $currency,
                    'base=' . $this->getBaseCurrency()
                ]);

            /* Запрос. */
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $requestUrl,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => 'GET'
            ]);
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            curl_close($ch);

            if (empty($result['rates'])) {
                throw new \Exception('Пришли пустые данные.');
            }

            return $result['rates'][$currency];
        });

        if ($reverse) {
            $value = bcdiv('1', $value, 4);
        }

        $result = bcmul((string)$amount, $value, 4);
        return (float)bcmul($result, '1', 2);
    }
}