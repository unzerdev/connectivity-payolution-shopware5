<?php

namespace PolPaymentPayolution\RiskManagement\Context;

/**
 * Class RiskCheckResult
 *
 * @package PolPaymentPayolution\RiskManagement\Context
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RiskCheckResult
{
    /**
     * @var bool
     */
    private $riskValue;

    /**
     * @var string
     */
    private $riskSource;

    /**
     * RiskCheckResult constructor.
     *
     * @param bool $riskValue
     * @param string $riskSource
     */
    public function __construct($riskValue, $riskSource)
    {
        $this->riskValue = $riskValue;
        $this->riskSource = $riskSource;
    }

    /**
     * Is RiskValue
     *
     * @return bool
     */
    public function isRiskValue()
    {
        return $this->riskValue;
    }

    /**
     * Get RiskSource
     *
     * @return string
     */
    public function getRiskSource()
    {
        return $this->riskSource;
    }
}