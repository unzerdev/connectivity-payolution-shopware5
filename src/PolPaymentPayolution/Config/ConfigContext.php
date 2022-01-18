<?php

namespace PolPaymentPayolution\Config;

use Enlight_Controller_Front;
use Enlight_Controller_Request_Request;
use PolPaymentPayolution\ComponentManager\ComponentManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConfigContext
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ConfigContext
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
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * @var ComponentManager
     */
    private $componentManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $cache;

    /**
     * @var ConfigContextProvider
     */
    private $configContextProvider;

    /**
     * The in memory cache for the default shop
     *
     * @var Shop|null
     */
    private $defaultShopCache;

    /**
     * ConfigContext constructor.
     *
     * @param Enlight_Controller_Front $front
     * @param ComponentManager $componentManager
     * @param ContainerInterface $container
     * @param ConfigContextProvider $configContextProvider
     */
    public function __construct(
        Enlight_Controller_Front $front,
        ComponentManager $componentManager,
        ContainerInterface $container,
        ConfigContextProvider $configContextProvider
    ) {
        $this->front = $front;
        $this->componentManager = $componentManager;
        $this->container = $container;
        $this->configContextProvider = $configContextProvider;
    }

    /**
     * Get Shop
     *
     * @return Shop
     */
    public function getShop()
    {
        $request = $this->front->Request();

        $shop = null;

        // On the cli we don't have an request, we need to return the default shop
        if ($request instanceof Enlight_Controller_Request_Request) {
            $shop = $this->getShopFromRequest($request);
        } else {
            $shop = $this->getDefaultShop();
        }

        return $shop;
    }

    /**
     * Get Shop from order
     *
     * @param int $orderId
     *
     * @return null|Shop
     */
    private function getShopFromOrder($orderId)
    {
        $shop = null;
        if ($order = $this->componentManager->getModelManager()->find(Order::class, $orderId)) {
            $shop = $order->getShop();
        }

        return $shop;
    }

    /**
     * Get Shop from Frontend
     *
     * @return Shop|null
     */
    private function getShopFromFrontend()
    {
        return $this->container->has('shop') ? $this->container->get('shop') : null;
    }

    /**
     * Get the shop from the request
     *
     * @param Enlight_Controller_Request_Request $request The shopware request
     *
     * @return Shop
     */
    private function getShopFromRequest(Enlight_Controller_Request_Request $request)
    {
        $module = $request->getModuleName();

        $requestIdentifier = $this->configContextProvider->getOrderId() ?:
            ($request->getParam('id') ?: $request->getParam('orderId'));

        $cacheKey = sha1(json_encode($request->getParams()) . $requestIdentifier);

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $shop = null;
        if ($module === self::FRONTEND) {
            $shop = $this->getShopFromFrontend();
        } elseif ($module === self::BACKEND && $requestIdentifier) {
            $shop = $this->getShopFromOrder($requestIdentifier);
        }

        if (!$shop) {
            $shop = $this->getDefaultShop();
        }

        $this->cache[$cacheKey] = $shop;

        return $shop;
    }

    /**
     * Get Default shop
     *
     * @return Shop|null
     */
    private function getDefaultShop()
    {
        if ($this->defaultShopCache === null) {
            $this->defaultShopCache =
                $this->componentManager->getModelManager()->getRepository(Shop::class)->getActiveDefault();
        }

        return $this->defaultShopCache;
    }
}