<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyManagerInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\ParserInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;
use SergeiIvchenko\CommissionTask\Parser\CSVStringParser;
use SergeiIvchenko\CommissionTask\Service\CurrencyExchanger\CurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\FeeCalculateStrategyManager;
use SergeiIvchenko\CommissionTask\Service\MathService;
use SergeiIvchenko\CommissionTask\Service\SimpleStorage;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\DepositFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return array(
    /** Parameters **/
    'parameters.base_currency' => CurrencyExchangerInterface::EUR,
    'parameters.base_no_fee' => '1000',
    'parameters.calculation_accuracy' => 2,

    'parameters.fee_value.deposit_strategy' => '0.0003',
    'parameters.fee_value.private_withdraw' => '0.003',
    'parameters.fee_value.business_withdraw' => '0.005',

    'parameters.exchange_rates_api.url' => 'http://api.exchangeratesapi.io/v1',
    'parameters.exchange_rates_api.version' => 'latest',
    'parameters.exchange_rates_api.key' => '825f95bd9432d91ed6ceb33e6b764929',

    /** Services. */
    StorageInterface::class => new SimpleStorage(),
    'currency_cache' => new ArrayAdapter(),
    MathServiceInterface::class => function (ContainerInterface $container) {
        return new MathService($container->get('parameters.calculation_accuracy'));
    },
    CurrencyExchanger::class => DI\autowire()
        ->constructorParameter('baseCurrency', DI\get('parameters.base_currency'))
        ->constructorParameter('baseNoFee', DI\get('parameters.base_no_fee')),
    CurrencyExchangerInterface::class => DI\get(CurrencyExchanger::class),

    //Fee calculate strategies
    DepositFeeCalculateStrategy::class => DI\autowire()
        ->constructorParameter('feeValue', DI\get('parameters.fee_value.deposit_strategy')),
    PrivateWithdrawFeeCalculateStrategy::class => DI\autowire()
        ->constructorParameter('feeValue', DI\get('parameters.fee_value.private_withdraw')),
    BusinessWithdrawFeeCalculateStrategy::class => DI\autowire()
        ->constructorParameter('feeValue', DI\get('parameters.fee_value.business_withdraw')),
    FeeCalculateStrategyManager::class => DI\create()
        ->method('addStrategy', DI\get(DepositFeeCalculateStrategy::class))
        ->method('addStrategy', DI\get(PrivateWithdrawFeeCalculateStrategy::class))
        ->method('addStrategy', DI\get(BusinessWithdrawFeeCalculateStrategy::class)),
    FeeCalculateStrategyManagerInterface::class => DI\get(FeeCalculateStrategyManager::class),


    TaskService::class => function (ContainerInterface $container) {
        return new TaskService(
            $container->get(FeeCalculateStrategyManagerInterface::class),
            $container->get(StorageInterface::class)
        );
    },

    //Logger
    Logger::class => function (ContainerInterface $c) {
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../var/dev.log', Logger::DEBUG));
        return $logger;
    },
    LoggerInterface::class => DI\get(Logger::class),

    //Parser
    CSVStringParser::class => DI\create(CSVStringParser::class),
    ParserInterface::class => DI\get(CSVStringParser::class),

    //HTTPClient
    HttpClient::class => DI\factory(function ($apiUri, $apiKey, $baseCurrency) {
        return HttpClient::createForBaseUri($apiUri, [
            'query' => [
                'access_key' => $apiKey,
                'base' => $baseCurrency
            ]
        ]);
    })
        ->parameter('apiKey', DI\get('parameters.exchange_rates_api.key'))
        ->parameter('baseCurrency', DI\get('parameters.base_currency'))
        ->parameter('apiUri', DI\get('parameters.exchange_rates_api.url')),
    HttpClientInterface::class => DI\get(HttpClient::class)
);