<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyManagerInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;
use SergeiIvchenko\CommissionTask\Service\CurrencyExchanger\CurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\CurrencyExchanger\FakeCurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\FeeCalculateStrategyManager;
use SergeiIvchenko\CommissionTask\Service\MathService;
use SergeiIvchenko\CommissionTask\Service\SimpleStorage;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\DepositeFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

return array(
    /** Parameters **/
    'parameters.base_currency' => CurrencyExchangerInterface::EUR,
    'parameters.base_no_fee' => 1000,
    'parameters.calculation_accuracy' => 2,

    'parameters.fee_value.deposite_strategy' => 0.0003,
    'parameters.fee_value.private_withdraw' => 0.003,
    'parameters.fee_value.business_withdraw' => 0.005,

    'parameters.exchange_rates_api.url' => 'http://api.exchangeratesapi.io/v1',
    'parameters.exchange_rates_api.version' => 'latest',
    'parameters.exchange_rates_api.key' => $_SERVER['EXCHANGE_RATE_API_KEY'],

    /** Services. */
    StorageInterface::class => new SimpleStorage(),
    'currency_cache' => new ArrayAdapter(),
    MathServiceInterface::class => function (ContainerInterface $container) {
        return new MathService($container->get('parameters.calculation_accuracy'));
    },
    CurrencyExchanger::class => DI\autowire()
        ->constructorParameter('cache', DI\get('currency_cache'))
        ->constructorParameter('baseCurrency', DI\get('parameters.base_currency'))
        ->constructorParameter('baseNoFee', DI\get('parameters.base_no_fee'))
        ->constructorParameter('apiUrl', DI\get('parameters.exchange_rates_api.url'))
        ->constructorParameter('apiVersion', DI\get('parameters.exchange_rates_api.version'))
        ->constructorParameter('apiKey', DI\get('parameters.exchange_rates_api.key')),
    CurrencyExchangerInterface::class => DI\get(CurrencyExchanger::class),
    DepositeFeeCalculateStrategy::class => DI\autowire()
        ->constructorParameter('feeValue', DI\get('parameters.fee_value.deposite_strategy')),
    PrivateWithdrawFeeCalculateStrategy::class => DI\autowire()
        ->constructorParameter('feeValue', DI\get('parameters.fee_value.private_withdraw')),
    BusinessWithdrawFeeCalculateStrategy::class => DI\autowire()
        ->constructorParameter('feeValue', DI\get('parameters.fee_value.business_withdraw')),
    FeeCalculateStrategyManager::class => DI\create()
        ->method('addStrategy', DI\get(DepositeFeeCalculateStrategy::class))
        ->method('addStrategy', DI\get(PrivateWithdrawFeeCalculateStrategy::class))
        ->method('addStrategy', DI\get(BusinessWithdrawFeeCalculateStrategy::class)),
    FeeCalculateStrategyManagerInterface::class => DI\get(FeeCalculateStrategyManager::class),
    TaskService::class => function (ContainerInterface $container) {
        return new TaskService(
            $container->get(FeeCalculateStrategyManagerInterface::class),
            $container->get(StorageInterface::class)
        );
    }
);