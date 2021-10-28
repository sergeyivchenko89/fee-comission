<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service;

use DateTime;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;

class SimpleStorage implements StorageInterface
{
    private $dict;

    private $weekStart;

    public function __construct()
    {
        $this->dict = [];
    }

    public function save(OperationInterface $operation): void
    {
        $this->dict[$this->getKey($operation)][] = $operation;
    }

    public function getKey(OperationInterface $operation): string
    {
        return sprintf('%d|%s', $operation->getUserId(), $operation->getOperationType());
    }

    public function getRelatedOperations(OperationInterface $operation): array
    {
        $key = $this->getKey($operation);
        return $this->dict[$key] ?? [];
    }

    public function invalidate(OperationInterface $operation): void
    {
        $operationDate = new DateTime($operation->getDate());

        if (
            !isset($this->weekStart) ||
            ($operationDate->diff($this->weekStart)->days > 7)
        ) {
            $this->dict = [];
            $this->weekStart = new DateTime(sprintf('%s monday this week', $operation->getDate()));
        }
    }
}