<?php

namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use PolPaymentPayolution\Setup\Installer;

/**
 * Subscriber for the plugin config
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class PluginConfigSubscriber implements SubscriberInterface
{
    /**
     * The plugin installer
     *
     * @var Installer
     */
    private $installer;

    /**
     * PluginConfigSubscriber constructor.
     *
     * @param Installer $installer
     */
    public function __construct(Installer $installer)
    {
        $this->installer = $installer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @inheritdoc
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware\Models\Shop\Shop::postRemove' => 'removeOrphanShopValues',
            'Shopware\Models\Shop\Currency::postRemove' => 'removeOrphanCurrencyValues'
        ];
    }

    /**
     * Remove orphaned shop values
     *
     * @return void
     */
    public function removeOrphanShopValues()
    {
        $this->installer->removeOrphanedSubshopConfigValues();
    }

    /**
     * Remove orphaned currency values
     *
     * @return void
     */
    public function removeOrphanCurrencyValues()
    {
        $this->installer->removeOrphanedCurrencyConfigValues();
    }
}