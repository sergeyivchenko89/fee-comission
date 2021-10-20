<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface FeeCalculateStrategyManagerInterface {

    public function addStrategy(FeeCalculateStrategyInterface $strategy): void;

    public function getFee(OperationInterface $operation): float;

}