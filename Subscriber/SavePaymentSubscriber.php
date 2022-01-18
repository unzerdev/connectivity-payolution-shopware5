<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Hook_HookArgs;
use Exception;
use PolPaymentPayolution\Payment\SavePaymentHandler;

/**
 * Subscriber for all events regarding the changing or selecting of the payment methods
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class SavePaymentSubscriber implements SubscriberInterface
{
    /**
     * Payment handler
     *
     * @var SavePaymentHandler
     */
    private $savePaymentHandler;

    /**
     * SavePaymentSubscriber constructor.
     *
     * @param SavePaymentHandler $savePaymentHandler
     */
    public function __construct(SavePaymentHandler $savePaymentHandler)
    {
        $this->savePaymentHandler = $savePaymentHandler;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Frontend_Checkout::saveShippingPaymentAction::before' => 'processPaymentHandler',
            'Shopware_Controllers_Frontend_Account::savePaymentAction::before' => 'processPaymentHandler'
        ];
    }

    /**
     * Process the payment Handler
     *
     * @param Enlight_Hook_HookArgs $args
     *
     * @return void
     */
    public function processPaymentHandler(Enlight_Hook_HookArgs $args)
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        $this->savePaymentHandler->process($controller->Request()->getParams());
    }
}
