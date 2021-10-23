<?php

declare(strict_types=1);

use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Service\CurrencyExchanger\CurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\CurrencyExchanger\FakeCurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\FeeCalculateStrategyManager;
use SergeiIvchenko\CommissionTask\Service\IOService;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use SergeiIvchenko\CommissionTask\Service\SimpleStorage;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\DepositeFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

$filePath = $argv[1];

(new Dotenv())->load(__DIR__ . '/.env');

$inputFilePath = realpath($argv[1]);
$outputFilePath = dirname(__FILE__) . '/'. $argv[2];

$storage = new SimpleStorage();
$currencyCache = new ArrayAdapter();

$currencyExchanger = new FakeCurrencyExchanger(CurrencyExchangerInterface::EUR, 1000);
$currencyExchanger = new CurrencyExchanger(
    $currencyCache,
    CurrencyExchangerInterface::EUR,
    1000,
    'http://api.exchangeratesapi.io/v1',
    'latest',
    $_SERVER['EXCHANGE_RATE_API_KEY']
);

//Стратегия стратегии расчета комиссии
$feeStrategyManager = new FeeCalculateStrategyManager();
$feeStrategyManager->addStrategy(new DepositeFeeCalculateStrategy(0.0003));
$feeStrategyManager->addStrategy(new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchanger, 0.003));
$feeStrategyManager->addStrategy(new BusinessWithdrawFeeCalculateStrategy(0.005));

$operationTaskService = new TaskService($feeStrategyManager, $storage);
$ioService = new IOService($inputFilePath, $outputFilePath);
/** @var OperationInterface $operation */
foreach ($ioService->getRawItemData() as $operation) {
    $ioService->outputData((string) $operationTaskService->getFee($operation));
}

//TODO Закрыть файловые дескрипторы
