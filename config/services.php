<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;
use SergeiIvchenko\CommissionTask\Service\CurrencyExchanger\FakeCurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\FeeCalculateStrategyManager;
use SergeiIvchenko\CommissionTask\Service\SimpleStorage;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\DepositeFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

return array(
    'parameters.base_currency' => CurrencyExchangerInterface::EUR,
    'parameters.base_no_fee' => 1000,
    'parameters.fee_value.deposite_strategy' => 0.0003,
    'parameters.fee_value.private_withdraw' => 0.003,
    'parameters.fee_value.business_withdraw' => 0.005,
    CurrencyExchangerInterface::class => function (ContainerInterface $c) {
        return new FakeCurrencyExchanger(
            $c->get('parameters.base_currency'),
            $c->get('parameters.base_no_fee')
        );
    },
    StorageInterface::class => new SimpleStorage(),
    'currencyCache' => new ArrayAdapter(),

    // Стратегии выбора подсчета нлогов
    DepositeFeeCalculateStrategy::class => function (ContainerInterface $container) {
        return new DepositeFeeCalculateStrategy($container->get('parameters.fee_value.deposite_strategy'));
    },
    PrivateWithdrawFeeCalculateStrategy::class => function (ContainerInterface $container) {
        return new PrivateWithdrawFeeCalculateStrategy(
            $container->get(StorageInterface::class),
            $container->get(CurrencyExchangerInterface::class),
            $container->get('parameters.fee_value.private_withdraw')
        );
    },
    BusinessWithdrawFeeCalculateStrategy::class => function (ContainerInterface $container) {
        return new BusinessWithdrawFeeCalculateStrategy(
            $container->get('parameters.fee_value.business_withdraw')
        );
    },
    FeeCalculateStrategyManager::class => DI\create()
        ->method('addStrategy', DI\get(DepositeFeeCalculateStrategy::class))
        ->method('addStrategy', DI\get(PrivateWithdrawFeeCalculateStrategy::class))
        ->method('addStrategy', DI\get(BusinessWithdrawFeeCalculateStrategy::class)),
    TaskService::class => function (ContainerInterface $container) {
        return new TaskService(
            $container->get(FeeCalculateStrategyManager::class),
            $container->get(StorageInterface::class)
        );
    }
);