<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Exception;

use Exception;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class FeeCalculateStrategyNotFound extends Exception
{
    public function __construct(OperationInterface $operation)
    {
        parent::__construct("Стратегия для {$operation->getClientType()} клиента и {$operation->getOperationType()} операции не найдена.");
    }
}