<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Exception;

use Exception;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class FeeCalculateStrategyNotFound extends Exception
{
    public function __construct(OperationInterface $operation)
    {
        parent::__construct(
            "Strategy for {$operation->getClientType()} client and {$operation->getOperationType()} operation not found."
        );
    }
}