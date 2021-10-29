<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use SergeiIvchenko\CommissionTask\App;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Service\IOService;
use SergeiIvchenko\CommissionTask\Service\TaskService;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

$inputFilePath = realpath($argv[1]);
$outputFilePath = dirname(__FILE__) . '/'. $argv[2];

try {
    (new App())->run($inputFilePath, $outputFilePath);
} catch (\Exception $e) {
    echo "========\r\n";
    echo sprintf("ERROR: %s\r\n", $e->getMessage());
    echo "========\r\n";
}
