<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Contracts;

interface IOServiceInterface
{
    public function getRawItemData();

    public function outputData(string $data): void;
}