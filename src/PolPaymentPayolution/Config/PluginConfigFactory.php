<?php

namespace PolPaymentPayolution\Config;

use Shopware\Components\Plugin\ConfigReader;
use PolPaymentPayolution\PolPaymentPayolution as Bootstrap;

/**
 * Class PluginConfigFactory
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PluginConfigFactory
{
    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var ConfigContext
     */
    private $configContext;

    /**
     * PluginConfigFactory constructor.
     *
     * @param ConfigReader $configReader
     * @param ConfigContext $configContext
     */
    public function __construct(ConfigReader $configReader, ConfigContext $configContext)
    {
        $this->configReader = $configReader;
        $this->configContext = $configContext;
    }

    /**
     * Get Config
     *
     * @return PluginConfig
     */
    public function getConfig()
    {
        $config = $this->configReader->getByPluginName(
            //Todo: replace with dic constant PAYOL-259
            'PolPaymentPayolution',
            $this->configContext->getShop()
        );

        return new PluginConfig($config);
    }

}
