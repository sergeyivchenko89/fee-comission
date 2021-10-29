<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Service\IOService;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

$filePath = $argv[1];

/* Load our DI container. */
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/services.php');
$container = $containerBuilder->build();

$inputFilePath = realpath($argv[1]);
$outputFilePath = dirname(__FILE__) . '/'. $argv[2];
$operationTaskService = $container->get(TaskService::class);
$ioService = new IOService($inputFilePath, $outputFilePath);
/** @var OperationInterface $operation */
foreach ($ioService->getRawItemData() as $operation) {
    $ioService->outputData((string) $operationTaskService->getFee($operation));
}

//TODO Закрыть файловые дескрипторы
