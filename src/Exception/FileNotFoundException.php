<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Exception;

use Exception;

class FileNotFoundException extends Exception
{
    public function __construct($filePath)
    {
        parent::__construct("File $filePath not found.");
    }
}