<?php

namespace PolPaymentPayolution\Config;

/**
 * Class ConfigContextProvider
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ConfigContextProvider
{
    /**
     * Frontend constant
     *
     * @var string
     */
    const FRONTEND = 'frontend';

    /**
     * Backend constant
     *
     * @var string
     */
    const BACKEND = 'backend';

    /**
     * Module Name e.g Backend|Frontend|Widget
     *
     * @var string
     */
    private $module;

    /**
     * @var int|null
     */
    private $orderId;

    /**
     * ConfigContextProvider constructor.
     *
     * @param string $module
     * @param int|null $orderId
     */
    public function __construct($module = self::FRONTEND, $orderId = null)
    {
        $this->module = $module;
        $this->orderId = $orderId;
    }

    /**
     * Get Module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set Module
     *
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Get OrderId
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set order id
     *
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }
}