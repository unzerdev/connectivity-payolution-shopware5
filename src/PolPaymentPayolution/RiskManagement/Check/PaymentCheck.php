<?php

namespace PolPaymentPayolution\RiskManagement\Check;

use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;

/**
 * Class PaymentCheck
 *
 * @package PolPaymentPayolution\RiskManagement\Check
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PaymentCheck implements RiskCheckInterface
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
        $paymentWhiteList = [
           'payolution_invoice_b2c',
           'payolution_invoice_b2b',
           'payolution_installment',
           'payolution_elv'
        ];

        if (!in_array($context->getPaymentName(), $paymentWhiteList, true)) {
            throw new RiskSkipException('invalid payment');
        }

        return new RiskCheckResult($context->isInitialRisk(), 'payment');
    }
}