<?php

namespace PolPaymentPayolution\Payment\Capture;

use PolPaymentPayolution\Payment\Order\Amount;

/**
 * Class WorkflowAmount
 *
 * @package PolPaymentPayolution\Payment\Capture
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowAmount
{
    /**
     * @var Amount
     */
    private $captureAmount;

    /**
     * @var Amount
     */
    private $refundAmount;

    /**
     * @var string
     */
    private $captureSnippet;

    /**
     * @var string
     */
    private $refundSnippet;

    /**
     * @var Amount
     */
    private $orderAmount;

    /**
     * WorkflowAmount constructor.
     *
     * @param Amount $captureAmount
     * @param Amount $refundAmount
     * @param Amount $orderAmount
     * @param string $captureSnippet
     * @param string $refundSnippet
     */
    public function __construct(
        Amount $captureAmount,
        Amount $refundAmount,
        Amount $orderAmount,
        $captureSnippet,
        $refundSnippet
    ) {
        $this->captureAmount = $captureAmount;
        $this->refundAmount = $refundAmount;
        $this->captureSnippet = $captureSnippet;
        $this->refundSnippet = $refundSnippet;
        $this->orderAmount = $orderAmount;
    }

    /**
     * Get CaptureAmount
     *
     * @return Amount
     */
    public function getCaptureAmount()
    {
        return $this->captureAmount;
    }

    /**
     * Get RefundAmount
     *
     * @return Amount
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * Get OrderAmount
     *
     * @return Amount
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    /**
     * Get CaptureSnippet
     *
     * @return string
     */
    public function getCaptureSnippet()
    {
        return sprintf($this->captureSnippet, $this->captureAmount->getNormalizedAmount());
    }

    /**
     * Get RefundSnippet
     *
     * @return string
     */
    public function getRefundSnippet()
    {
        return sprintf($this->refundSnippet, $this->refundAmount->getNormalizedAmount());
    }

    /**
     * Is Total Captured
     *
     * @return bool
     */
    public function isRefundActive()
    {
        return $this->refundAmount->getValue() !== (float) 0;
    }

    /**
     * Is Total Refunded
     *
     * @return bool
     */
    public function isCaptureActive()
    {
        return $this->captureAmount->getValue() !== (float) 0;
    }

    /**
     * Get Order Refund Difference
     *
     * @return float
     */
    public function getOrderRefundDifference()
    {
        return $this->orderAmount->getValue() - $this->refundAmount->getValue();
    }

    /**
     * Get Order Capture Difference
     *
     * @return float
     */
    public function getOrderCaptureDifference()
    {
        return $this->orderAmount->getValue() - $this->captureAmount->getValue();
    }
}