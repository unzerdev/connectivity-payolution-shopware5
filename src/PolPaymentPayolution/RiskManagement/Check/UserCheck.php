<?php

namespace PolPaymentPayolution\RiskManagement\Check;

use Payolution\Config\Config;
use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;

/**
 * Class UserCheck
 *
 * @package PolPaymentPayolution\RiskManagement\Check
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class UserCheck implements RiskCheckInterface
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
        $user = $context->getUser();

        if (empty($user['billingaddress']['firstname'])) {
            throw new RiskSkipException('invalid user');
        }

        $hasCompany = !empty($user['billingaddress']['company']);

        $risk = $context->isInitialRisk();

        if (!$hasCompany && $context->getPaymentShortName() === 'PAYOLUTION_INVOICE_B2B') {
            $risk = true;
        }

        $isPrivatePayment = $this->isPaymentPrivate($context->getPaymentShortName());

        $riskSource = 'user';
        if ($hasCompany && $isPrivatePayment) {
            $risk = true;
            $riskSource = 'company';
        }

        if (isset($user['billingaddress'], $user['shippingaddress']) &&
            !$context->getConfig()->isDifferentAddresses() &&
            !$this->checkDifferenceAddresses($user['billingaddress'], $user['shippingaddress'])
        ) {
                $risk = true;
                $riskSource = 'address';
        }

        return new RiskCheckResult($risk, $riskSource);
    }

    /**
     * Check Difference Address
     *
     * @param array $address1
     * @param array $address2
     *
     * @return bool
     */
    private function checkDifferenceAddresses(array $address1, array $address2)
    {
        $result = false;
        if (isset($address1['id'], $address2['id'])) {
            $result = $address1['id'] === $address2['id'];
        }

        return $result;
    }

    /**
     * Check if Payment is Private
     *
     * @param string $paymentShortName
     *
     * @return bool
     */
    private function isPaymentPrivate($paymentShortName)
    {
        return (
            $paymentShortName === 'PAYOLUTION_INVOICE'
            || $paymentShortName === 'PAYOLUTION_INS'
            || $paymentShortName === 'PAYOLUTION_ELV'
        );
    }
}