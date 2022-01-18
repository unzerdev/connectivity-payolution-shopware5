<?php
namespace PolPaymentPayolution;

use PolPaymentPayolution\PolPaymentPayolution as Bootstrap;
use Shopware\Components\Plugin\ConfigReader;

class GetPluginConfig
{
    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * GetPluginConfig constructor.
     *
     * @param ConfigReader $configReader
     */
    public function __construct(ConfigReader $configReader)
    {
        $this->configReader = $configReader;
    }

    /**
     * get Plugin Config
     *
     * @return array
     */
    public function getPluginConfig()
    {
        return $this->configReader->getByPluginName('PolPaymentPayolution');
    }

}