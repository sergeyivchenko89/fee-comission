<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Tests\FeeCalculateStrategy\Strategy;

use PHPUnit\Framework\TestCase;
use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;
use SergeiIvchenko\CommissionTask\Service\FakeCurrencyExchanger;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;

class PrivateWithdrawFeeCalculateStrategyTest extends TestCase
{
    private const AMOUNT_BASE_NO_FEE = 1000;

    public function testCanApply()
    {
        //Declare Mock objects
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = $this->createMock(CurrencyExchangerInterface::class);
        $mathService = $this->createMock(MathServiceInterface::class);
        $operation = $this->createMock(OperationInterface::class);

        $operation->expects($this->exactly(1))->method('getOperationType')
            ->willReturn(FeeCalculateStrategyInterface::WITHDRAW);
        $operation->expects($this->exactly(1))->method('getClientType')
            ->willReturn(FeeCalculateStrategyInterface::PRIVATE);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $mathService, 0.003);
        $this->assertTrue($strategy->canApply($operation));
    }

    public function testGetFeeWithEmptyStorage(): void
    {
        //Declare Mock objects
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = $this->createMock(CurrencyExchangerInterface::class);
        $mathService = $this->createMock(MathServiceInterface::class);
        $testedOperation = $this->getOperationFromArray(['private', 'withdraw', '200', 'EUR'], 2);

        //$storage behavior
        $storage->expects($this->once())->method('getRelatedOperations')->willReturn([]);

        //$currencyExchangerService behavior
        $currencyExchangerService->expects($this->exactly(2))->method('getBaseNoFee')->willReturn(
            self::AMOUNT_BASE_NO_FEE
        );
        $currencyExchangerService->expects($this->exactly(2))->method('convert')
            ->withConsecutive([200., 'EUR'], [0., 'EUR', true])
            ->willReturnOnConsecutiveCalls(200., 0.);

        //$mathService behavior
        $mathService->expects($this->exactly(2))->method('comp')
            ->withConsecutive([0.0, self::AMOUNT_BASE_NO_FEE], [-800.0, 0.0])
            ->willReturnOnConsecutiveCalls(-1.0, -1.0);
        $mathService->expects($this->exactly(1))->method('add')
            ->withConsecutive([0.0, 200.0])
            ->willReturnOnConsecutiveCalls(200.0);
        $mathService->expects($this->exactly(1))->method('sub')
            ->withConsecutive(
                [200.0, 1000.0]
            )
            ->willReturnOnConsecutiveCalls(-800.0);
        $mathService->expects($this->exactly(1))->method('mul')
            ->withConsecutive(
                [0.0, 0.003]
            )->willReturnOnConsecutiveCalls(0.0);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $mathService, 0.003);
        $this->assertSame(0.0, $strategy->getFee($testedOperation));
    }

    public function testGetFeeWithThreeItemsInStorage(): void
    {
        //Declare Mock objects
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = $this->createMock(CurrencyExchangerInterface::class);
        $mathService = $this->createMock(MathServiceInterface::class);
        $testedOperation = $this->createMock(OperationInterface::class);

        //$operation behavior
        $testedOperation->expects($this->exactly(1))->method('getAmount')->willReturn(200);

        //$storage behavior
        $items = [];
        foreach (
            [
                ['private', 'withdraw', '200', 'EUR'],
                ['private', 'withdraw', '200', 'EUR'],
                ['private', 'withdraw', '200', 'EUR']
            ] as $data
        ) {
            $operationMock = $this->createMock(OperationInterface::class);
            $items[] = $operationMock;
        }
        $storage->expects($this->exactly(1))->method('getRelatedOperations')->willReturn($items);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $mathService, 0.003);
        $this->assertSame(0.0, $strategy->getFee($testedOperation));
    }

    public function testGetFeeWithLessThreeItemsInStorage1()
    {
        //Declare Mock objects
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = $this->createMock(CurrencyExchangerInterface::class);
        $mathService = $this->createMock(MathServiceInterface::class);
        $testedOperation = $this->createMock(OperationInterface::class);

        //$testedOperation behavior
        $testedOperation->expects($this->exactly(2))->method('getCurrency')->willReturn('EUR');
        $testedOperation->expects($this->exactly(1))->method('getAmount')->willReturn(200);

        //$storage behavior
        $storageItem = $this->createMock(OperationInterface::class);
        $storageItem->expects($this->exactly(1))->method('getAmount')->willReturn(200);
        $storageItem->expects($this->exactly(1))->method('getCurrency')->willReturn('EUR');
        $storage->expects($this->exactly(1))->method('getRelatedOperations')->willReturn([$storageItem]);

        //$currencyExchangerService behavior
        $currencyExchangerService->expects($this->exactly(2))->method('getBaseNoFee')->willReturn(
            self::AMOUNT_BASE_NO_FEE
        );
        $currencyExchangerService->expects($this->exactly(3))->method('convert')
            ->withConsecutive([200, 'EUR'], [200.0, 'EUR'], [0.0, 'EUR', true])
            ->willReturnOnConsecutiveCalls(200., 200.0, 0.0);

        //$mathService behavior
        $mathService->expects($this->exactly(2))->method('add')
            ->withConsecutive([0.0, 200.0], [200., 200.]
            )->willReturnOnConsecutiveCalls(200., 400.);
        $mathService->expects($this->exactly(2))->method('comp')
            ->withConsecutive([200., self::AMOUNT_BASE_NO_FEE], [-600.0, 0.0])
            ->willReturnOnConsecutiveCalls(-1.0, -1.0);
        $mathService->expects($this->exactly(1))->method('sub')
            ->withConsecutive([400., self::AMOUNT_BASE_NO_FEE])
            ->willReturnOnConsecutiveCalls(-600.);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $mathService, 0.003);
        $this->assertSame(0.0, $strategy->getFee($testedOperation));
    }

    public function testGetFeeWithLessThreeItemsInStorage2()
    {
        //Declare Mock objects
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = $this->createMock(CurrencyExchangerInterface::class);
        $mathService = $this->createMock(MathServiceInterface::class);
        $testedOperation = $this->createMock(OperationInterface::class);

        //$testedOperation behavior
        $testedOperation->expects($this->exactly(2))->method('getCurrency')->willReturn('EUR');
        $testedOperation->expects($this->exactly(1))->method('getAmount')->willReturn(200);

        //$storage behavior
        $storedItemAmountValue = 900.;
        $storageItem = $this->createMock(OperationInterface::class);
        $storageItem->expects($this->exactly(1))->method('getAmount')->willReturn($storedItemAmountValue);
        $storageItem->expects($this->exactly(1))->method('getCurrency')->willReturn('EUR');
        $storage->expects($this->exactly(1))->method('getRelatedOperations')->willReturn([$storageItem]);

        //$currencyExchangerService behavior
        $currencyExchangerService->expects($this->exactly(2))->method('getBaseNoFee')->willReturn(
            self::AMOUNT_BASE_NO_FEE
        );
        $currencyExchangerService->expects($this->exactly(3))->method('convert')
            ->withConsecutive([$storedItemAmountValue, 'EUR'], [200.0, 'EUR'], [100.0, 'EUR', true])
            ->willReturnOnConsecutiveCalls($storedItemAmountValue, 200.0, 100.0);

        //$mathService behavior
        $mathService->expects($this->exactly(2))->method('add')
            ->withConsecutive([0.0, $storedItemAmountValue], [900., 200.])
            ->willReturnOnConsecutiveCalls($storedItemAmountValue, 1100.);
        $mathService->expects($this->exactly(2))->method('comp')
            ->withConsecutive([$storedItemAmountValue, self::AMOUNT_BASE_NO_FEE], [100.0, 0.0])
            ->willReturnOnConsecutiveCalls(-1.0, 1.0);
        $mathService->expects($this->exactly(1))->method('sub')
            ->withConsecutive([1100., self::AMOUNT_BASE_NO_FEE])
            ->willReturnOnConsecutiveCalls(100.);
        $mathService->expects($this->exactly(1))->method('mul')
            ->withConsecutive([100.0, 0.003])
            ->willReturnOnConsecutiveCalls(0.3);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $mathService, 0.003);
        $this->assertSame(0.3, $strategy->getFee($testedOperation));
    }

    public function testGetFeeWithLessThreeItemsInStorage3()
    {
        //Declare Mock objects
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = $this->createMock(CurrencyExchangerInterface::class);
        $mathService = $this->createMock(MathServiceInterface::class);
        $testedOperation = $this->createMock(OperationInterface::class);

        //$testedOperation behavior
        $testedOperation->expects($this->exactly(1))->method('getAmount')->willReturn(200);

        //$storage behavior
        $storedItemAmountValue = 1100.;
        $storageItem = $this->createMock(OperationInterface::class);
        $storageItem->expects($this->exactly(1))->method('getAmount')->willReturn($storedItemAmountValue);
        $storageItem->expects($this->exactly(1))->method('getCurrency')->willReturn('EUR');
        $storage->expects($this->exactly(1))->method('getRelatedOperations')->willReturn([$storageItem]);

        //$currencyExchangerService behavior
        $currencyExchangerService->expects($this->exactly(1))->method('getBaseNoFee')->willReturn(
            self::AMOUNT_BASE_NO_FEE
        );
        $currencyExchangerService->expects($this->exactly(1))->method('convert')
            ->withConsecutive([$storedItemAmountValue, 'EUR'])
            ->willReturnOnConsecutiveCalls($storedItemAmountValue);

        //$mathService behavior
        $mathService->expects($this->exactly(1))->method('add')
            ->withConsecutive([0.0, $storedItemAmountValue])
            ->willReturnOnConsecutiveCalls($storedItemAmountValue);
        $mathService->expects($this->exactly(1))->method('comp')
            ->withConsecutive([$storedItemAmountValue, self::AMOUNT_BASE_NO_FEE])
            ->willReturnOnConsecutiveCalls(1);
        $mathService->expects($this->exactly(1))->method('mul')
            ->withConsecutive([200.0, 0.003])
            ->willReturnOnConsecutiveCalls(0.6);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $mathService, 0.003);
        $this->assertSame(0.6, $strategy->getFee($testedOperation));
    }

    protected function getOperationFromArray(array $data, int $getCurrencyQueriesCount): OperationInterface
    {
        $operationMock = $this->createMock(OperationInterface::class);
        $operationMock->expects($this->exactly(1))->method('getAmount')->willReturn($data[2]);
        $operationMock->expects($this->exactly($getCurrencyQueriesCount))->method('getCurrency')->willReturn($data[3]);
        return $operationMock;
    }
}