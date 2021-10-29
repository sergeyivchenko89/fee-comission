<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
use SergeiIvchenko\CommissionTask\Contracts\MathServiceInterface;
use SergeiIvchenko\CommissionTask\Contracts\OperationInterface;
use SergeiIvchenko\CommissionTask\Contracts\StorageInterface;

class PrivateWithdrawFeeCalculateStrategy extends AbstractFeeCalculateStrategy
{
    private $storage;

    /**
     * @var CurrencyExchangerInterface $currencyExchanger
     */
    private $currencyExchanger;

    public function __construct(
        StorageInterface $storage,
        CurrencyExchangerInterface $currencyExchanger,
        MathServiceInterface $mathService,
        string $feeValue
    ) {
        parent::__construct($mathService, $feeValue);
        $this->storage = $storage;
        $this->currencyExchanger = $currencyExchanger;
    }

    public function canApply(OperationInterface $operation): bool
    {
        return self::WITHDRAW === $operation->getOperationType()
            && self::PRIVATE === $operation->getClientType();
    }

    protected function getAmount2Fee(OperationInterface $operation): string
    {
        $finishedOperations = $this->storage->getRelatedOperations($operation);

        if (count($finishedOperations) >= 3) {
            return $operation->getAmount();
        }

        $amountsSum = array_reduce($finishedOperations, function (string $prevValue, OperationInterface $operation) {
            return $this->mathService->add(
                $prevValue,
                $this->currencyExchanger->convert(
                    $operation->getAmount(),
                    $operation->getCurrency()
                )
            );
        }, '0');

        if (0 < $this->mathService->comp($amountsSum, $this->currencyExchanger->getBaseNoFee())) {
            return $operation->getAmount();
        }

        $operationAmountInBaseCurrency = $this->currencyExchanger->convert(
            $operation->getAmount(),
            $operation->getCurrency()
        );

        $amountsSum = $this->mathService->add($amountsSum, $operationAmountInBaseCurrency);

        $amount2Fee = $this->mathService->sub($amountsSum, $this->currencyExchanger->getBaseNoFee());
        $amount2Fee = (0 < $this->mathService->comp($amount2Fee, '0') ? $amount2Fee : '0');

        return $this->currencyExchanger->convert($amount2Fee, $operation->getCurrency(), true);
    }
}