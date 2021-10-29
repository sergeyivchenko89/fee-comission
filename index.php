<?php

declare(strict_types=1);

use SergeiIvchenko\CommissionTask\App;

require __DIR__.'/vendor/autoload.php';

$inputFilePath = realpath($argv[1]);
$outputFilePath = dirname(__FILE__) . '/'. $argv[2];

(new App())->run($inputFilePath, $outputFilePath);
