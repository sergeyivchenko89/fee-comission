<?php

declare(strict_types=1);

use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Service\HelperService;
use SergeiIvchenko\CommissionTask\Exception\FileNotFoundException;
use SergeiIvchenko\CommissionTask\Parser\CSVStringParser;
use SergeiIvchenko\CommissionTask\Service\FakeCurrencyExchanger;
use SergeiIvchenko\CommissionTask\Service\FeeCalculateStrategyManager;
use SergeiIvchenko\CommissionTask\Service\IOService;
use SergeiIvchenko\CommissionTask\Service\MathService;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use SergeiIvchenko\CommissionTask\Service\SimpleStorage;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\DepositeFeeCalculateStrategy;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;

require __DIR__.'/vendor/autoload.php';

$filePath = $argv[1];

$inputFilePath = realpath($argv[1]);
$outputFilePath = dirname(__FILE__) . '/'. $argv[2];

$storage = new SimpleStorage();

$currencyExchanger = new FakeCurrencyExchanger(FakeCurrencyExchanger::EUR, 1000);

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
