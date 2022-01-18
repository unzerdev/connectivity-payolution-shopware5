<?php

namespace PolPaymentPayolution\Config;

/**
 * Class PluginConfig
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PluginConfig
{
    /**
     * @var array
     */
    private $config;

    /**
     * PluginConfig constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getInitialOrderStateB2C()
    {
        return $this->config['b2cOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderStateB2C()
    {
        return $this->config['b2cOrderStateCapture'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateB2C()
    {
        return $this->config['b2cOrderStateRefund'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getInitialOrderStateB2B()
    {
        return $this->config['b2bOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderStateB2B()
    {
        return $this->config['b2bOrderStateCapture'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateB2B()
    {
        return $this->config['b2bOrderStateRefund'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getOrderStateInstallment()
    {
        return $this->config['installmentOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateInstallment()
    {
        return $this->config['installmentOrderStateRefund'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getInitialOrderStateELV()
    {
        return $this->config['elvOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderStateELV()
    {
        return $this->config['elvOrderStateCapture'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateELV()
    {
        return $this->config['elvOrderStateRefund'];
    }

    /**
     * Is Order Automatic Refund after delete active
     *
     * @return bool
     */
    public function isAutomaticOrderRefund()
    {
        return (bool) $this->config['automaticRefundOrder'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderState()
    {
        return $this->config['refundOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderState()
    {
        return $this->config['captureOrderState'];
    }

    /**
     * Is Automatic Order Capture active
     *
     * @return bool
     */
    public function isAutomaticCaptureOrders()
    {
        return (bool) $this->config['automaticCaptureAfterOrder'];
    }

    /**
     * Is Automatic Order refund active
     *
     * @return bool
     */
    public function isAutomaticRefundCancellationPositions()
    {
        return (bool) $this->config['automaticRefundPositionsPickware'];
    }

    /**
     * Is History Simple View
     *
     * @return bool
     */
    public function isHistorySimpleView()
    {
        return (bool) $this->config['simpleHistoryMessages'];
    }
}
