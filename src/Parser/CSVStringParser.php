<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Parser;

use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Contracts\ParserInterface;
use SergeiIvchenko\CommissionTask\Entity\Operation\OperationItem;

class CSVStringParser implements ParserInterface
{
    private static $instance;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new CSVStringParser();
        }

        return self::$instance;
    }

    public function getOperation(string $stringData): OperationInterface
    {
        $parts = explode(',', $stringData);
        return new OperationItem(
            $parts[0],
            (int)$parts[1],
            $parts[2],
            $parts[3],
            $parts[4],
            $parts[5]
        );
    }
}