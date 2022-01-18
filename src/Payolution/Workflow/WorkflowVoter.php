<?php

namespace Payolution\Workflow;

use PolPaymentPayolution\Payment\Capture\WorkflowAmountProvider;
use Psr\Log\LoggerInterface;

/**
 * Class WorkflowVoter
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowVoter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var WorkflowAmountProvider
     */
    private $amountProvider;

    /**
     * WorkflowVoter constructor.
     *
     * @param LoggerInterface $logger
     * @param WorkflowAmountProvider $amountProvider
     */
    public function __construct(LoggerInterface $logger, WorkflowAmountProvider $amountProvider)
    {
        $this->logger = $logger;
        $this->amountProvider = $amountProvider;
    }

    /**
     * Check if Refund Order is allowed
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function isWholeRefundAllowed($orderId)
    {
        $amount = $this->amountProvider->getWorkflowAmount($orderId);

        $result = $amount->getOrderRefundDifference() === 0.00 || !$amount->isRefundActive();
        if (!$result) {
            $this->logger->error(sprintf('Payolution Refund Operation for order %s not allowed', $orderId));
        }

        return $result;
    }

    /**
     * Check if Capture Order is allowed
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function isWholeCaptureAllowed($orderId)
    {
        $amount = $this->amountProvider->getWorkflowAmount($orderId);

        $result = $amount->getOrderCaptureDifference() === 0.00 || !$amount->isCaptureActive();
        if (!$result) {
            $this->logger->error(sprintf('Payolution Capture Operation for order %s not allowed', $orderId));
        }

        return $result;
    }
}