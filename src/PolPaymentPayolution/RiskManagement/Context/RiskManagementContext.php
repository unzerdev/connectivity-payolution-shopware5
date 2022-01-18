<?php

namespace PolPaymentPayolution\RiskManagement\Context;

use PolPaymentPayolution\Config\Config;

/**
 * Class RiskManagementContext
 *
 * @package PolPaymentPayolution\RiskManagement\Context
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class RiskManagementContext
{
    /**
     * @var bool
     */
    private $initialRisk;

    /**
     * @var int
     */
    private $paymentId;

    /**
     * @var array
     */
    private $basket;

    /**
     * @var array
     */
    private $user;

    /**
     * @var string
     */
    private $paymentName;

    /**
     * @var string
     */
    private $paymentShortName;

    /**
     * @var Config
     */
    private $config;

    /**
     * RiskManagementContext constructor.
     *
     * @param Config $config
     * @param bool $initialRisk
     * @param int $paymentId
     * @param array $basket
     * @param array $user
     * @param string $paymentName
     * @param string $paymentShortName
     */
    public function __construct(
        Config $config,
        $initialRisk,
        $paymentId,
        array $basket,
        array $user,
        $paymentName,
        $paymentShortName
    ) {
        $this->initialRisk = $initialRisk;
        $this->paymentId = $paymentId;
        $this->basket = $basket;
        $this->user = $user;
        $this->paymentName = $paymentName;
        $this->paymentShortName = $paymentShortName;
        $this->config = $config;
    }

    /**
     * Get Config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Is InitialRisk
     *
     * @return bool
     */
    public function isInitialRisk()
    {
        return $this->initialRisk;
    }

    /**
     * Get PaymentId
     *
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Get Basket
     *
     * @return array
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Get User
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get PaymentName
     *
     * @return string
     */
    public function getPaymentName()
    {
        return $this->paymentName;
    }

    /**
     * Get PaymentShortName
     *
     * @return string
     */
    public function getPaymentShortName()
    {
        return $this->paymentShortName;
    }
}
