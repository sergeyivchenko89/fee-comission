<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Tests\Strategy\FeeCalculateStrategy;

use PHPUnit\Framework\TestCase;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\DepositFeeCalculateStrategy;

class DepositFeeCalculateStrategyTest extends TestCase
{
    public function testCanApply()
    {
        $mathService = $this->createMock(MathServiceInterface::class);
        $operation = $this->createMock(OperationInterface::class);
        $operation->expects($this->once())->method('getOperationType')->willReturn('deposit');

        $strategy = new DepositFeeCalculateStrategy($mathService, '0.005');
        $this->assertTrue($strategy->canApply($operation));
    }

    public function testGetFee()
    {
        $mathService = $this->createMock(MathServiceInterface::class);
        $operation = $this->createMock(OperationInterface::class);
        $operation->expects($this->once())->method('getAmount')->willReturn(1000);
        $strategy = new DepositFeeCalculateStrategy($mathService, '0.005');

        //$mathService behavior
        $mathService->expects($this->exactly(1))->method('mul')->withConsecutive([1000.0, 0.005])
            ->willReturnOnConsecutiveCalls(5.0);

        $this->assertEquals('5.0', $strategy->getFee($operation));
    }
}