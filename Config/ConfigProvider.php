<?php

namespace PolPaymentPayolution\Config;

use PolPaymentPayolution\Shop\ShopProvider;
use Doctrine\DBAL\Connection;
use PDO;
use Shopware\Components\Plugin\ConfigReader;
use Shopware\Models\Shop\Currency;
use Shopware\Models\Shop\Shop;

/**
 * Provider for the config
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ConfigProvider
{
    /**
     * @var ShopProvider
     */
    private $shopProvider;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * The cache for the plugin config
     *
     * @var array
     */
    private $pluginConfigCache = [];

    /**
     * Cache for the config
     *
     * @var array
     */
    private $configCache = [];

    /**
     * ConfigProvider constructor.
     *
     * @param ShopProvider $shopProvider
     * @param ConfigReader $configReader
     * @param Connection $connection
     * @param string $pluginName
     */
    public function __construct(ShopProvider $shopProvider, ConfigReader $configReader, Connection $connection, $pluginName)
    {
        $this->shopProvider = $shopProvider;
        $this->configReader = $configReader;
        $this->connection = $connection;
        $this->pluginName = $pluginName;
    }

    /**
     * Get the config for the current or default shop
     *
     * The current shop will be used if an shop entity is found in the dic
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->createConfig($this->shopProvider->getCurrentShop());
    }

    /**
     * Get the config or null from an given order id
     *
     * @param int $orderId
     *
     * @return Config|null
     */
    public function getConfigByOrderId($orderId)
    {
        $config = null;
        if ($shop = $this->shopProvider->getShopForOrder($orderId)) {
            $config = $this->createConfig($shop);
        }

        return $config;
    }

    /**
     * Create Config from given shop
     *
     * @param Shop $shop
     *
     * @return Config
     */
    private function createConfig(Shop $shop)
    {
        // Check if currency is valid, if not set default currency as fallback
        if ($shop->getCurrency() instanceof Currency) {
            $currencyId = $shop->getCurrency()->getId();


            $cacheKey = md5($shop->getId() . $currencyId);

            if (!isset($this->configCache[$cacheKey])) {
                $expressionBuilder = $this->connection->getExpressionBuilder();
                $queryBuilder = $this->connection->createQueryBuilder();
                $queryBuilder
                    ->from('bestit_payolution_config', 'bpc')
                    ->select('bpc.name, bpc.value')
                    ->where($expressionBuilder->eq('bpc.shopId', ':shopId'))
                    ->andWhere($expressionBuilder->eq('bpc.currencyId', ':currencyId'))
                    ->setParameters(
                        [
                            'currencyId' => $currencyId,
                            'shopId' => $shop->getId(),
                        ]
                    );

                $dynamicConfigArray = $queryBuilder->execute()->fetchAll(PDO::FETCH_KEY_PAIR);
                $this->configCache[$cacheKey] = $this->buildConfig(
                    $dynamicConfigArray,
                    $this->getPluginConfig($shop),
                    $shop
                );
             }
            $config = $this->configCache[$cacheKey];
        } else {
            $config = $this->buildConfig([], [], $shop);
        }

        return $config;
    }

    /**
     * Get the plugin config for the shop
     *
     * @param Shop $shop
     *
     * @return array
     */
    private function getPluginConfig(Shop $shop)
    {
        $shopId = $shop->getId();
        if (!isset($this->pluginConfigCache[$shopId])) {
            $this->pluginConfigCache[$shopId] = $this->configReader->getByPluginName(
                $this->pluginName,
                $shop
            );
        }

        return $this->pluginConfigCache[$shopId];
    }

    /**
     * Build the plugin config from the dynamic config array and the cached shopware plugin config
     *
     * @param array $dynamicConfigParameters
     * @param array $pluginConfigParameter
     * @param Shop $shop
     *
     * @return Config
     */
    private function buildConfig(array $dynamicConfigParameters, array $pluginConfigParameter, Shop $shop)
    {
        return new Config(
            array_merge($dynamicConfigParameters, $pluginConfigParameter),
            $shop
        );
    }
}