<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Tests\FeeCalculateStrategy\Strategy;

use PHPUnit\Framework\TestCase;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;

class BusinessWithdrawFeeCalculateStrategyTest extends TestCase
{
    public function testCanApply()
    {
        $operation = $this->createMock(OperationInterface::class);
        $operation->expects($this->once())->method('getOperationType')
            ->willReturn(FeeCalculateStrategyInterface::WITHDRAW);
        $operation->expects($this->once())->method('getClientType')
            ->willReturn(FeeCalculateStrategyInterface::BUSINESS);
        $strategy = new BusinessWithdrawFeeCalculateStrategy(0.005);

        $this->assertTrue($strategy->canApply($operation));
    }

    public function testGetFee()
    {
        $operation = $this->createMock(OperationInterface::class);
        $operation->expects($this->once())->method('getAmount')->willReturn(1000);
        $strategy = new BusinessWithdrawFeeCalculateStrategy(0.005);

        $this->assertEquals(5.0, $strategy->getFee($operation));
    }
}