<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Contracts;

interface OperationInterface
{
    public function getDate(): string;

    public function getUserId(): int;

    public function getClientType(): string;

    public function getOperationType(): string;

    public function getAmount(): float;

    public function getCurrency(): string;
}