<?php

declare(strict_types=1);


namespace SergeiIvchenko\CommissionTask\Strategy\FeeCalculateStrategy;

use SergeiIvchenko\CommissionTask\Contracts\CurrencyExchangerInterface;
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
        StorageInterface           $storage,
        CurrencyExchangerInterface $currencyExchanger,
        float                      $feeValue
    )
    {
        parent::__construct($feeValue);
        $this->storage = $storage;
        $this->currencyExchanger = $currencyExchanger;
    }

    public function canApply(OperationInterface $operation): bool
    {
        return self::WITHDRAW === $operation->getOperationType()
            && self::PRIVATE === $operation->getClientType();
    }

    protected function getAmount2Fee(OperationInterface $operation): float
    {
        $finishedOperations = $this->storage->getRelatedOperations($operation);

        if (count($finishedOperations) > 3) {
            return $operation->getAmount();
        }

        //Просуммируем предыдущие
        $amountsSum = array_reduce($finishedOperations, function (float $prevValue, OperationInterface $operation) {
            return (float)bcadd(
                (string)$prevValue,
                (string)$this->currencyExchanger->convert(
                    $operation->getAmount(),
                    $operation->getCurrency()
                ),
                2
            );
        }, 0);

        // Если сумма уже более безналоговой базы, то возвращаем стандартный расчет
        if (1 === bccomp((string)$amountsSum, (string)$this->currencyExchanger->getBaseNoFee(), 2)) {
            return $operation->getAmount();
        }

        $operationAmountInBaseCurrency = $this->currencyExchanger->convert($operation->getAmount(), $operation->getCurrency());

        // Прибавим к сумме стоимость операции
        $amountsSum = bcadd((string)$amountsSum, (string)$operationAmountInBaseCurrency, 2);

        //Если результат оказался более безналоговой базы, то вычтем ее
        $amount2Fee = bcsub((string)$amountsSum, (string)$this->currencyExchanger->getBaseNoFee(), 2);
        $amount2Fee = (float)(1 === bccomp($amount2Fee, '0', 2) ? $amount2Fee : 0);

        //возвращаем конвертированый остаток
        return $this->currencyExchanger->convert($amount2Fee, $operation->getCurrency(), true);
    }
}