<?php

namespace PolPaymentPayolution\Payment\Order\Factory;

use PolPaymentPayolution\Enum\OrderPosition as OrderPositionEnum;
use PolPaymentPayolution\Payment\Order\Amount;
use PolPaymentPayolution\Payment\Order\OrderPosition;
use PolPaymentPayolution\Payment\Order\OrderPositionIdentifier;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class RefundPositionFactory
 *
 * @package PolPaymentPayolution\Payment\Order\Factory
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RefundPositionFactory
{
    /**
     * Create From Element
     *
     * @param WorkflowElement $element
     * @param string $currency
     *
     * @return OrderPosition
     */
    public function createFromElement(WorkflowElement $element, $currency)
    {
        $amount = 0.00;
        $quantity = 0;
        if ($element->getCapturedQuantity() > 0 && !$element->isRefunded()) {
            $quantity = $element->getCapturedQuantity() - $element->getRefundedQuantity();
            $amount = ($element->getAmount() / $element->getQuantity()) * $quantity;
        }

        return new OrderPosition(
            new OrderPositionIdentifier($element->getIdentifier(), $element->getAdditionalIdentifier()),
            new Amount($amount, $currency),
            $element->getName(),
            $quantity
        );
    }

    /**
     * Create Absolute Element
     *
     * @param float $amount
     * @param string $name
     * @param string $currency
     *
     * @return OrderPosition
     */
    public function createAbsoluteElement($amount, $name, $currency)
    {
        return new OrderPosition(
            new OrderPositionIdentifier(OrderPositionEnum::DIFFERENCE_PRINCE),
            new Amount($amount, $currency),
            $name,
            1
        );
    }
}