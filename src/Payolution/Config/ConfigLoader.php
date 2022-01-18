<?php

namespace Payolution\Config;

use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Loader for the payolution config
 *
 * @package Payolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ConfigLoader
{
    /**
     * @var ComponentManagerInterface
     */
    protected $componentManager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * ConfigLoader constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param ContainerInterface $container
     */
    public function __construct(ComponentManagerInterface $componentManager, ContainerInterface $container)
    {
        $this->componentManager = $componentManager;
        $this->container = $container;
    }

    /**
     * Load config by given shop id
     *
     * @param int $shopId
     *
     * @return Config
     */
    public function loadConfigByShop($shopId)
    {
        $modelManager = $this->componentManager->getModelManager();
        $shop = $modelManager->getRepository(Shop::class)->find($shopId);

        return $this->loadConfig($shop);
    }

    /**
     * Load the config by given shop model
     *
     * @param Shop $shop
     *
     * @return Config
     */
    public function loadConfig(Shop $shop)
    {
        $shopId = $shop->getId();
        $config = null;
        if (!isset($this->cache[$shopId])) {
            $sql = <<<sql
SELECT `name`, `value`
FROM bestit_payolution_config
WHERE shopId = :shopId
  AND currencyId = :currencyId
sql;

            $modelManager = $this->componentManager->getModelManager();
            $connection = $modelManager->getConnection();

            $result = $connection->fetchAll($sql, [
                ':shopId' => $shop->getId(),
                ':currencyId' => $shop->getCurrency()->getId(),
            ]);

            $configResult = [];
            foreach ($result as $value) {
                if (isset($value['name'], $value['value'])) {
                    $configResult[$value['name']] = $value['value'];
                }
            }

            $config = (new Config())->setConfig($configResult);

            $this->cache[$shopId] = $config;
        }

        return $config ?: $this->cache[$shopId];
    }

    /**
     * Load Current Config
     *
     * @return Config
     */
    public function loadCurrentConfig()
    {
        $currentShop = $this->getCurrentShop();

        return $this->loadConfig($currentShop);
    }

    /**
     * Get Current Detached Shop
     *
     * @return DetachedShop
     */
    public function getCurrentShop()
    {
        $shop = $this->container->has('shop') ? $this->container->get('shop') : null;

        if (!$shop) {
            $modelManager = $this->componentManager->getModelManager();
            $shop = $modelManager->getRepository(Shop::class)->getActiveDefault();
        }

        return $shop;
    }
}
