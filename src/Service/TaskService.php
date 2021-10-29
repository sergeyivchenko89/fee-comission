<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Service;

use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyManagerInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;
use SergeiIvchenko\CommissionTask\Contracts\TaskServiceInterface;

class TaskService implements TaskServiceInterface
{
    /**
     * @var FeeCalculateStrategyManager
     */
    private $feeCalculateStrategyManager;

    /**
     * @var StorageInterface $storage
     */
    private $storage;

    public function __construct(
        FeeCalculateStrategyManagerInterface $feeCalculateStrategyManager,
        StorageInterface $storage
    ) {
        $this->feeCalculateStrategyManager = $feeCalculateStrategyManager;
        $this->storage = $storage;
    }

    public function getFee(OperationInterface $operation): string
    {
        $this->storage->invalidate($operation);
        $feeValue = $this->feeCalculateStrategyManager->getFee($operation);
        $this->storage->save($operation);
        return $feeValue;
    }
}