<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface StorageInterface
{
    public function save(OperationInterface $operation): void;

    public function getRelatedOperations(OperationInterface $operation): array;

    public function getKey(OperationInterface $operation): string;

    public function invalidate(OperationInterface $operation): void;
}