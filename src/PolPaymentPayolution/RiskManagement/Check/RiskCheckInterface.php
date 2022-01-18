<?php


namespace PolPaymentPayolution\RiskManagement\Check;

use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;


/**
 * Interface RiskCheckInterface
 *
 * @package PolPaymentPayolution\RiskManagement\Check
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
interface RiskCheckInterface
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
    public function checkRisk(RiskManagementContext $context);
}