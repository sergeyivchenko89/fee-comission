<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Tests\FeeCalculateStrategy\Strategy;

use PHPUnit\Framework\TestCase;
use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;
use SergeiIvchenko\CommissionTask\Service\FakeCurrencyExchanger;
use SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy\PrivateWithdrawFeeCalculateStrategy;

class PrivateWithdrawFeeCalculateStrategyTest extends TestCase
{
    public function getData(): array
    {
        return [
            [
                [
                    ['private', 'withdraw', '200', 'eur'],
                ],
                ['private', 'withdraw', '200', 'eur'],
                0.003,
                FakeCurrencyExchanger::EUR,
                1000,
                0,
            ],
            [
                [
                    ['private', 'withdraw', '900', 'eur'],
                ],
                ['private', 'withdraw', '200', 'eur'],
                0.003,
                FakeCurrencyExchanger::EUR,
                1000,
                0.3,
            ]
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testGetFee($existedOperationsData, $testOperationData, $feeComission, $baseCurrency, $amountNoFee, $feeValue)
    {
        $storage = $this->createMock(StorageInterface::class);
        $currencyExchangerService = new FakeCurrencyExchanger($baseCurrency, $amountNoFee);

        /* Заполним Storage списком обработанных операций. */
        $result = array_map(function ($data) {
            return $this->getOperationFromArray($data, 1);
        }, $existedOperationsData);
        $storage->expects($this->exactly(1))->method('getRelatedOperations')->willReturn($result);

        $testedOperation = $this->getOperationFromArray($testOperationData, 2);

        $strategy = new PrivateWithdrawFeeCalculateStrategy($storage, $currencyExchangerService, $feeComission);
        $this->assertEquals($feeValue, $strategy->getFee($testedOperation));
    }

    protected function getOperationFromArray(array $data, int $getCurrencyQueriesCount): OperationInterface
    {
        $operationMock = $this->createMock(OperationInterface::class);
        $operationMock->expects($this->exactly(1))->method('getAmount')->willReturn($data[2]);
        $operationMock->expects($this->exactly($getCurrencyQueriesCount))->method('getCurrency')->willReturn($data[3]);
        return $operationMock;
    }
}