<?php

namespace PolPaymentPayolution\RiskManagement\Check;

use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;

/**
 * Class RestrictionsCheck
 *
 * @package PolPaymentPayolution\RiskManagement\Check
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RestrictionsCheck implements RiskCheckInterface
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
        $risk = $context->isInitialRisk();

        switch ($context->getPaymentShortName()) {
            case 'PAYOLUTION_INVOICE':
                $countries = $context->getConfig()->getAllowedCountriesInvoiceB2C();
                break;
            case 'PAYOLUTION_INVOICE_B2B':
                $countries = $context->getConfig()->getAllowedCountriesInvoiceB2B();
                break;
            case 'PAYOLUTION_INS':
                $countries = $context->getConfig()->getAllowedCountriesInstallment();
                break;
            case 'PAYOLUTION_ELV':
                $countries = $context->getConfig()->getAllowedCountriesElv();
                break;
            default:
                $countries = $context->getConfig()->getAllowedCountriesInvoiceB2C();
        }

        $currencies = $context->getConfig()->getAllowedCurrencies();
        $user = $context->getUser();

        $countryIso = null;
        if (isset($user['additional']['country']['countryiso'])) {
            $countryIso = $user['additional']['country']['countryiso'];
        }

        $source = 'restrictions';
        if ($countryIso && !in_array($countryIso, $countries, true)) {
            $risk = true;
            $source = 'country';
        }

        if (!in_array($context->getConfig()->getShop()->getCurrency()->getCurrency(), $currencies, true)) {
            $risk = true;
            $source = 'currency';
        }

        return new RiskCheckResult($risk, $source);
    }
}