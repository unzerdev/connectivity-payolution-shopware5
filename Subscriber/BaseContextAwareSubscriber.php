<?php

namespace PolPaymentPayolution\Subscriber;

use PolPaymentPayolution\Config\ConfigContextProvider;
use Shopware\Components\Model\ModelManager;

/**
 * Base class for other subscribers.
 *
 * @package PolPaymentPayolution\Subscriber
 */
class BaseContextAwareSubscriber
{
    /**
     * @var ModelManager
     */
    protected $modelManager;

    /**
     * @var ConfigContextProvider
     */
    private $configContextProvider;

    /**
     * BaseContextAwareSubscriber constructor.
     *
     * @param ConfigContextProvider $configContextProvider
     * @param ModelManager $modelManager
     */
    public function __construct(ConfigContextProvider $configContextProvider, ModelManager $modelManager)
    {
        $this->configContextProvider = $configContextProvider;
        $this->modelManager = $modelManager;
    }

    /**
     * Set the context (backend or frontend)
     *
     * @param null $orderId
     *
     * @return void
     */
    protected function setContext($orderId = null)
    {
        $module = ConfigContextProvider::FRONTEND;

        if ($orderId) {
            $module = ConfigContextProvider::BACKEND;
            $this->configContextProvider->setOrderId($orderId);
        }

        $this->configContextProvider->setModule($module);
    }
}
