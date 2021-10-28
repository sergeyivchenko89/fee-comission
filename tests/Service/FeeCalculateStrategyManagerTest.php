<?php

declare(strict_types=1);

namespace SergeiIvchenko\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use SergeiIvchenko\CommissionTask\Contracts\FeeCalculateStrategyInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Exception\FeeCalculateStrategyNotFound;
use SergeiIvchenko\CommissionTask\Service\FeeCalculateStrategyManager;

class FeeCalculateStrategyManagerTest extends TestCase
{
    public function getData()
    {
        return [
            [
                [
                    [true, 4],
                    [false, 5],
                    [false, 6],
                ],
                4
            ],
            [
                [
                    [false, 4],
                    [true, 5],
                    [false, 6],
                ],
                5
            ],
            [
                [
                    [false, 4],
                    [false, 5],
                    [true, 6],
                ],
                6
            ],
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testGetFee($strategiesData, $result): void
    {
        $operation = $this->createMock(OperationInterface::class);

        $manager = new FeeCalculateStrategyManager();

        foreach ($strategiesData as $strategyData) {
            $strategy = $this->createMock(FeeCalculateStrategyInterface::class);
            $strategy->method('canApply')->willReturn($strategyData[0]);
            $strategy->method('getFee')->willReturn($strategyData[1]);
            $manager->addStrategy($strategy);
        }

        $this->assertEquals($result, $manager->getFee($operation));
    }

    public function getExceptionData(): array
    {
        return [
            [
                [
                    [false, 4],
                    [false, 5],
                    [false, 6],
                ],
                4
            ],
        ];
    }

    /**
     * @dataProvider getExceptionData
     */
    public function testGetFeeException($strategiesData, $result)
    {
        $operation = $this->createMock(OperationInterface::class);

        $manager = new FeeCalculateStrategyManager();

        foreach ($strategiesData as $strategyData) {
            $strategy = $this->createMock(FeeCalculateStrategyInterface::class);
            $strategy->method('canApply')->willReturn($strategyData[0]);
            $strategy->method('getFee')->willReturn($strategyData[1]);
            $manager->addStrategy($strategy);
        }

        $this->expectException(FeeCalculateStrategyNotFound::class);
        $manager->getFee($operation);
    }
}