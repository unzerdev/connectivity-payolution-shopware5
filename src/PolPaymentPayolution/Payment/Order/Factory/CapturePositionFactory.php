<?php

namespace PolPaymentPayolution\Payment\Order\Factory;

use PolPaymentPayolution\Payment\Order\Amount;
use PolPaymentPayolution\Payment\Order\OrderPosition;
use PolPaymentPayolution\Payment\Order\OrderPositionIdentifier;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class CapturePositionFactory
 *
 * @package PolPaymentPayolution\Payment\Order\Factory
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class CapturePositionFactory
{
    /**
     * Create from Element
     *
     * @param WorkflowElement $element
     * @param string $currency
     *
     * @return OrderPosition
     */
    public function createFromElement(WorkflowElement $element, $currency)
    {
        $amount = 0;
        $quantity = 0;
        if (!$element->isCaptured() && $element->getAmount() !== 0) {
            $quantity = $element->getQuantity() - $element->getCapturedQuantity();
            $amount = ($element->getAmount() / $element->getQuantity()) * $quantity;
        }

        return new OrderPosition(
            new OrderPositionIdentifier($element->getIdentifier(), $element->getAdditionalIdentifier()),
            new Amount($amount, $currency),
            $element->getName(),
            $quantity
        );
    }
}