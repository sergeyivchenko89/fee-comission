<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service;

use Exception;
use SergeiIvchenko\CommissionTask\Contracts\IOServiceInterface;
use SergeiIvchenko\CommissionTask\Exception\FileNotFoundException;
use SergeiIvchenko\CommissionTask\Parser\CSVStringParser;

class IOService implements IOServiceInterface
{
    private $fdInput;

    private $fdOutput;

    public function __construct(string $inputFileName, string $outputFileName)
    {
        try {
            if (!file_exists($inputFileName)) {
                throw new FileNotFoundException($inputFileName);
            }

            if (file_exists($outputFileName)) {
                unlink($outputFileName);
            }

            $this->fdInput = fopen($inputFileName, 'r');
            $this->fdOutput = fopen($outputFileName, 'a');
        } catch (Exception $e) {
            if (file_exists($outputFileName)) {
                unlink($outputFileName);
            }
        }
    }

    public function getRawItemData()
    {
        while (false !== ($line = fgets($this->fdInput))) {
            $line = trim($line);
            yield CSVStringParser::getInstance()->getOperation($line);
        }
    }

    public function outputData(string $data): void
    {
        fwrite($this->fdOutput, $data . "\r\n");
    }
}