<?php

declare(strict_types=1);

use SergeiIvchenko\CommissionTask\App;

require __DIR__ . '/vendor/autoload.php';

$inputFilePath = realpath($argv[1]);
$outputFilePath = dirname(__FILE__) . '/' . $argv[2];

if (false === $inputFilePath) {
    var_dump(sprintf('Ошибка чтения входного файла %s!\r\n', $argv[1]));
} else {
    (new App())->run($inputFilePath, $outputFilePath);
}

