<?php

namespace PolPaymentPayolution\Config;

use Payolution\Config\AbstractConfig;
use Payolution\Config\Config;
use PDO;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Models\Shop\Currency;

/**
 * Class ConfigFactory
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class LegacyConfigFactory
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigContext
     */
    private $context;

    /**
     * ConfigFactory constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param LoggerInterface $logger
     * @param ConfigContext $context
     */
    public function __construct(
        ComponentManagerInterface $componentManager,
        LoggerInterface $logger,
        ConfigContext $context
    ) {
        $this->componentManager = $componentManager;
        $this->logger = $logger;
        $this->context = $context;
    }

    /**
     * Get Config
     *
     * @return AbstractConfig
     */
    public function getConfig()
    {
        $shop = $this->context->getShop();

        $currencyId = null;
        // Check if currency is valid, if not set default currency as fallback
        if ($shop->getCurrency() instanceof Currency) {
            $currencyId = $shop->getCurrency()->getId();
            $config = $this->getConfigModel($shop->getId(), $currencyId);
        } else {
            $config = new Config();
        }

        return $config;
    }

    /**
     * Get Config
     *
     * @param int $shopId
     * @param int $currencyId
     *
     * @return AbstractConfig
     */
    public function getConfigModel($shopId, $currencyId)
    {
        $cacheKey = sha1($shopId . '-' . $currencyId);
        $cache = $this->componentManager->getCacheModule();

        if ($cacheItem = $cache->load($cacheKey)) {
            return $cacheItem;
        }

        $queryBuilder = $this->componentManager->getDbalConnection()->createQueryBuilder();
        $queryBuilder
            ->from('bestit_payolution_config', 'bpc')
            ->select('bpc.name, bpc.value')
            ->where($queryBuilder->expr()->eq('bpc.shopId', ':shopId'))
            ->andWhere($queryBuilder->expr()->eq('bpc.currencyId', ':currencyId'))
            ->setParameter('currencyId', $currencyId)
            ->setParameter('shopId', $shopId);

        $result = $queryBuilder->execute()->fetchAll(PDO::FETCH_KEY_PAIR);

        if (count($result) === 0) {
            $this->logger->error(
                sprintf(
                    'ConfigFactory Error: no config for shop %s and currency %s found',
                    $shopId,
                    $currencyId
                )
            );

            // return empty Config for the Install process
            return new Config();
        }

        $config = (new Config())->setConfig($result);

        $cache->save($config, $cacheKey, ['payment_payolution'], 86400);

        return $config;
    }
}