<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface TaskServiceInterface
{
    public function getFee(OperationInterface $operation): float;
}