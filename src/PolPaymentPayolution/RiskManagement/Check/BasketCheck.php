<?php

namespace PolPaymentPayolution\RiskManagement\Check;

use Payolution\Config\Config;
use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;

/**
 * Class BasketCheck
 *
 * @package PolPaymentPayolution\RiskManagement\Check
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class BasketCheck implements RiskCheckInterface
{
    /**
     * Check Risk
     *
     * @param RiskManagementContext $context
     *
     * @return RiskCheckResult
     *
     * @throws RiskSkipException
     */
    public function checkRisk(RiskManagementContext $context)
    {
        $basket = $context->getBasket();

        if ($basket === [] || !isset($basket['AmountNumeric'])) {
            throw new RiskSkipException('invalid basket');
        }

        $basketAmount = (float) $basket['AmountNumeric'];

        list($minValue, $maxValue) = $this->getMinMaxValues($context);

        $risk = false;
        if ($basketAmount < $minValue || $basketAmount > $maxValue) {
            $risk = true;
        }

        return new RiskCheckResult($risk, 'basket_values');
    }

    /**
     * Get Min Max Values
     *
     * @param RiskManagementContext $context
     * @return array
     */
    private function getMinMaxValues(RiskManagementContext $context)
    {
        $minValue = 0;
        $maxValue = 99999999999;

        switch ($context->getPaymentName()) {
            case 'payolution_invoice_b2c':
                $minValue = $context->getConfig()->getMinB2cValue();
                $maxValue = $context->getConfig()->getMaxB2cValue();
                break;
            case 'payolution_invoice_b2b':
                $minValue = $context->getConfig()->getMinB2bValue();
                $maxValue = $context->getConfig()->getMaxB2bValue();
                break;
            case 'payolution_installment':
                $minValue = $context->getConfig()->getMinInstallmentValue();
                $maxValue = $context->getConfig()->getMaxInstallmentValue();
                break;
            case 'payolution_elv':
                $minValue = $context->getConfig()->getMinElvValue();
                $maxValue = $context->getConfig()->getMaxElvValue();
                break;
        }

        return [(float) $minValue, (float) $maxValue];
    }
}