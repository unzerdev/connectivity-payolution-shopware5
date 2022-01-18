<?php

namespace PolPaymentPayolution\Shop;

use Shopware\Models\Order\Order;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Shopware\Models\Order\Repository as OrderRepository;
use Shopware\Models\Shop\Repository as ShopRepository;

/**
 * Provider for the shop model
 *
 * @package PolPaymentPayolution\Shop
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ShopProvider implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * Cache for the resolved order id shops
     *
     * @var array
     */
    private $orderShopCache = [];

    /**
     * ShopProvider constructor.
     *
     * @param ShopRepository $shopRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(ShopRepository $shopRepository, OrderRepository $orderRepository)
    {
        $this->shopRepository = $shopRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get the shop from the shopware dic or returns the active default shop
     *
     * @return Shop
     */
    public function getCurrentShop()
    {
        // We need to inject the container directly because the shop service will only be injected in the container
        // in the frontend dispatch process of the shop
        if ($this->container->has('shop')) {
            $shop = $this->container->get('shop');
        } else {
            $shop = $this->shopRepository->getActiveDefault();
        }

        return $shop;
    }

    /**
     * Get Shop entity or null from an given order id
     *
     * @param int $orderId
     *
     * @return Shop|null
     */
    public function getShopForOrder($orderId)
    {
        if (!isset($this->orderShopCache[$orderId])) {
            $shop = null;

            /** @var Order $order */
            if ($order = $this->orderRepository->find($orderId)) {
                $shop = $order->getShop();
            }

            $this->orderShopCache[$orderId] = $shop;
        }

        return $this->orderShopCache[$orderId];
    }
}