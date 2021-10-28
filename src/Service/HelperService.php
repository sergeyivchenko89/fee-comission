<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Service;

use DateTime;
use SergeiIvchenko\CommissionTask\Contracts\HelperServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;

class HelperService implements HelperServiceInterface
{

    protected static $instance;

    protected function __construct()
    {
    }

    public static function get(): HelperServiceInterface
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getFormattedDate(OperationInterface $operation, string $format): string
    {
        $dt = (new DateTime())->setTimestamp(strtotime($operation->getDate()));
        return $dt->format($format);
    }
}