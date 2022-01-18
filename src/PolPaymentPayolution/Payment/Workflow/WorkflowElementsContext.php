<?php

namespace PolPaymentPayolution\Payment\Workflow;

use Payolution\Config\Config;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use Shopware\Models\Shop\Shop;

/**
 * Class WorkflowElementsContext
 *
 * @package PolPaymentPayolution\Payment\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowElementsContext
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * @var WorkflowPositionContext[]
     */
    private $elements;

    /**
     * WorkflowElementsContext constructor.
     *
     * @param Config $config
     * @param Shop $shop
     * @param PluginConfig $pluginConfig
     * @param WorkflowPositionContext[] $elements
     */
    public function __construct(Config $config, Shop $shop, PluginConfig $pluginConfig, array $elements)
    {
        $this->config = $config;
        $this->shop = $shop;
        $this->pluginConfig = $pluginConfig;
        $this->elements = $elements;
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
     * Get Shop
     *
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Get PluginConfig
     *
     * @return PluginConfig
     */
    public function getPluginConfig()
    {
        return $this->pluginConfig;
    }

    /**
     * Get Elements
     *
     * @return WorkflowPositionContext[]
     */
    public function getElements()
    {
        return $this->elements;
    }
}