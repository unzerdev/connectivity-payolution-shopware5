<?php

namespace PolPaymentPayolution\Payment;

use Exception;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PaymentProvider
 *
 * @package PolPaymentPayolution\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PaymentProvider
{
    /**
     * Fallback Currency
     *
     * @var string
     */
    const FALLBACK_CURRENCY = 'EURO';

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * PaymentProvider constructor.
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
     * Check If Payment is Active
     *
     * @param string $name
     *
     * @return bool
     */
    public function isPaymentActive($name)
    {
        try {
            /**
             * @var DetachedShop $shop
             */
            $shop = $this->container->get('shop');
            $shopId = $shop->getId();
        } catch (Exception $e) {
            $shopId = null;
        }

        $repo = $this->componentManager->getModelManager()->getRepository(Payment::class);

        /** @var Payment $model */
        if (!$model = $repo->findOneBy(['name' => $name])) {
            return false;
        }

        $active = $model->getActive();
        $modelShops = $model->getShops();

        $shopIds = $modelShops->map(function(Shop $shop) {
            return $shop->getId();
        });

        return $active && (count($modelShops) === 0 || in_array($shopId, $shopIds, true));
    }

    /**
     * Get Current Currency
     *
     * @return array
     */
    public function getCurrentCurrency()
    {
        $symbol = '';
        try {
            /**
             * @var DetachedShop $shop
             */
            $shop = $this->container->get('shop');
            $iso = $shop->getCurrency()->getCurrency();
            $symbol = $shop->getCurrency()->getSymbol();
        } catch (Exception $e) {
            $iso = self::FALLBACK_CURRENCY;
        }

        return [
            'iso' => $iso,
            'symbol' => $symbol
        ];
    }
}
