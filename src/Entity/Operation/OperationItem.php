<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Entity\Operation;

use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class OperationItem implements OperationInterface
{
    private $date;

    private $userId;

    private $clientType;

    private $operationType;

    private $amount;

    private $currency;

    public function __construct(
        string $date,
        int $userId,
        string $clientType,
        string $operationType,
        float $amount,
        string $currency
    ) {
        $this->date = $date;
        $this->userId = $userId;
        $this->clientType = strtolower($clientType);
        $this->operationType = strtolower($operationType);
        $this->amount = $amount;
        $this->currency = strtolower($currency);
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getClientType(): string
    {
        return $this->clientType;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}