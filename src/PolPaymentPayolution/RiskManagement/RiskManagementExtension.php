<?php

namespace PolPaymentPayolution\RiskManagement;

use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Check\RiskCheckInterface;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;
use Psr\Log\LoggerInterface;

/**
 * Class RiskManagementExtension
 *
 * @package PolPaymentPayolution\RiskManagement
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RiskManagementExtension
{
    /**
     * @var RiskCheckInterface[]
     */
    private $riskChecks;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RiskManagementExtension constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Add Risk Checks
     *
     * @param RiskCheckInterface $check
     */
    public function addRiskCheck(RiskCheckInterface $check)
    {
        $this->riskChecks[] = $check;
    }

    /**
     * Check Risks
     *
     * @param RiskManagementContext $context
     *
     * @return RiskCheckResult
     */
    public function checkRisk(RiskManagementContext $context)
    {

        $riskValue = $context->isInitialRisk();
        $riskSource = 'none';
        foreach ($this->riskChecks as $check) {
            try {
                $riskResult = $check->checkRisk($context);
            } catch (RiskSkipException $e) {
                $riskSource = get_class($check);
                $riskValue = $context->isInitialRisk();
                break;
            }

            if ($riskResult->isRiskValue()) {
                $riskValue = true;
                $riskSource = $riskResult->getRiskSource();
                break;
            }
        }

        $checkResult = new RiskCheckResult(
            $riskValue,
            $riskSource
        );

        return $checkResult;
    }
}