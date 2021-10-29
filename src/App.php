<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask;

use DI\ContainerBuilder;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Service\IOService;
use SergeiIvchenko\CommissionTask\Service\TaskService;

class App
{
    public function run(string $inputFilePath, string $outputFilePath)
    {
        /* Load our DI container. */
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . '/../config/services.php');
        $container = $containerBuilder->build();

        $operationTaskService = $container->get(TaskService::class);
        $ioService = new IOService($inputFilePath, $outputFilePath);
        /** @var OperationInterface $operation */
        foreach ($ioService->getRawItemData() as $operation) {
            $ioService->outputData((string)$operationTaskService->getFee($operation));
        }
    }
}