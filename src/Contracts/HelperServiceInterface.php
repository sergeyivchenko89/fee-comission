<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface HelperServiceInterface
{
    public function getFormattedDate(OperationInterface $operation, string $format): string;
}