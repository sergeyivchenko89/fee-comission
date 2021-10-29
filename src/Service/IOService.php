<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service;

use SergeiIvchenko\CommissionTask\Contracts\IOServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\ParserInterface;
use SergeiIvchenko\CommissionTask\Exception\FileNotFoundException;
use SergeiIvchenko\CommissionTask\Parser\CSVStringParser;

class IOService implements IOServiceInterface
{
    private $fInput;

    private $fOutput;

    /**
     * @var ParserInterface
     */
    private $parser;

    public function __construct(
        ParserInterface $parser,
        string $inputFileName,
        string $outputFileName
    ) {
        $this->fInput = $inputFileName;
        $this->fOutput = $outputFileName;
        $this->parser = $parser;

        if (file_exists($this->fOutput)) {
            unlink($this->fOutput);
        }
    }

    public function getRawItemData()
    {
        if (!file_exists($this->fInput)) {
            throw new FileNotFoundException($this->fInput);
        }

        $fd = fopen($this->fInput, 'r');

        try {
            while (false !== ($line = fgets($fd))) {
                $line = trim($line);
                yield $this->parser->getOperation($line);
            }
        } finally {
            fclose($fd);
        }
    }

    public function outputData(string $data): void
    {
        file_put_contents($this->fOutput, sprintf("%s\r\n", $data), FILE_APPEND);
    }
}