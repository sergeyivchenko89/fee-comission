<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Tests\FeeCalculateStrategy\Strategy;

use PHPUnit\Framework\TestCase;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\BusinessWithdrawFeeCalculateStrategy;

class BusinessWithdrawFeeCalculateStrategyTest extends TestCase
{
    private const FEE_VALUE = 0.005;

    public function testCanApply()
    {
        $mathService = $this->createMock(MathServiceInterface::class);
        $operation = $this->createMock(OperationInterface::class);
        $operation->expects($this->once())->method('getOperationType')
            ->willReturn(FeeCalculateStrategyInterface::WITHDRAW);
        $operation->expects($this->once())->method('getClientType')
            ->willReturn(FeeCalculateStrategyInterface::BUSINESS);
        $strategy = new BusinessWithdrawFeeCalculateStrategy($mathService, self::FEE_VALUE);

        $this->assertTrue($strategy->canApply($operation));
    }

    public function testGetFee()
    {
        $mathService = $this->createMock(MathServiceInterface::class);
        $operation = $this->createMock(OperationInterface::class);
        $operation->expects($this->once())->method('getAmount')->willReturn(1000);
        $strategy = new BusinessWithdrawFeeCalculateStrategy($mathService, self::FEE_VALUE);
        $mathService->expects($this->once())->method('mul')->willReturn(5.0);

        $this->assertEquals(5.0, $strategy->getFee($operation));
    }
}