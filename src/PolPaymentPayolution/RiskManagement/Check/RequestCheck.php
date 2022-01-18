<?php

namespace PolPaymentPayolution\RiskManagement\Check;

use Enlight_Controller_Front;
use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\RiskManagement\Context\RiskCheckResult;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;

/**
 * Class RequestCheck
 *
 * @package PolPaymentPayolution\RiskManagement\Check
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RequestCheck implements RiskCheckInterface
{
    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * @var array
     */
    private $actionWhiteList = [
        'shippingPayment'
    ];

    /**
     * RequestCheck constructor.
     *
     * @param Enlight_Controller_Front $front
     */
    public function __construct(Enlight_Controller_Front $front)
    {
        $this->front = $front;
    }

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
        $request = $this->front->Request();

        if ('checkout' !== $request->getControllerName()) {
            throw new RiskSkipException('invalid controller');
        }

        if (!in_array($request->getActionName(), $this->actionWhiteList, true)) {
            throw new RiskSkipException('invalid action');
        }

        return new RiskCheckResult($context->isInitialRisk(), 'request');
    }
}