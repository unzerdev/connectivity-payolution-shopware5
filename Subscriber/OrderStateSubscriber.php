<?php

namespace PolPaymentPayolution\Subscriber;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Enlight_Hook_HookArgs;
use Exception;
use PolPaymentPayolution\Config\ConfigContextProvider;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\State\OrderStateChangeHandler;
use PolPaymentPayolution\State\StateChangeContextFactory;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;

/**
 * Change order state
 *
 * @package PolPaymentPayolution\Subscriber
 */
class OrderStateSubscriber extends BaseContextAwareSubscriber implements SubscriberInterface
{
    /**
     * Handler for order state changes
     *
     * @var OrderStateChangeHandler
     */
    private $orderStateChangeHandler;

    /**
     * Factory for state changes
     *
     * @var StateChangeContextFactory
     */
    private $stateChangeContextFactory;

    /**
     * The plugin config
     *
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * OrderPostUpdateSubscriber constructor.
     *
     * @param OrderStateChangeHandler $orderStateChangeHandler
     * @param StateChangeContextFactory $stateChangeContextFactory
     * @param ConfigContextProvider $configContextProvider
     * @param ModelManager $modelManager
     * @param PluginConfig $pluginConfig
     */
    public function __construct(
        OrderStateChangeHandler $orderStateChangeHandler,
        StateChangeContextFactory $stateChangeContextFactory,
        ConfigContextProvider $configContextProvider,
        ModelManager $modelManager,
        PluginConfig $pluginConfig
    ) {
        parent::__construct($configContextProvider, $modelManager);

        $this->orderStateChangeHandler = $orderStateChangeHandler;
        $this->stateChangeContextFactory = $stateChangeContextFactory;
        $this->modelManager = $modelManager;
        $this->pluginConfig = $pluginConfig;
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
            'Shopware\Models\Order\Order::postUpdate' => 'updateState',
            'Shopware\Models\Order\Order::preRemove' => 'cancelOrder',
            'sOrder::setOrderStatus::before' => 'setStatus'
        ];
    }

    /**
     * Updates the order.
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function updateState(Enlight_Event_EventArgs $args)
    {
        /** @var Order $model */
        $model = $args->get('entity');
        if (!empty($model)) {
            $orderId = $model->getId();
            $this->setContext($orderId);
            $this->executeOrderStateChange($model);
        }
    }

    /**
     * Triggers a cancellation, when the order is deleted.
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return void
     *
     * @throws Exception
     */
    public function cancelOrder(Enlight_Event_EventArgs $args)
    {
        /** @var Order $model */
        $model = $args->get('entity');
        $orderId = $model->getId();
        $this->setContext($orderId);

        if ($model->getPayment()->getAction() === 'PolPaymentPayolution') {
            if ($this->pluginConfig->isAutomaticOrderRefund()) {
                $mode = $this->orderStateChangeHandler->getCancelMode($orderId);

                $this->orderStateChangeHandler->invokeAutomaticOrderProcess($model, $mode);
            }
        }
    }

    /**
     * Update order state change.
     *
     * @param Enlight_Hook_HookArgs $args
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws Exception
     */
    public function setStatus(Enlight_Hook_HookArgs $args)
    {
        $orderId = $args->get('orderId');
        $orderStatusId = $args->get('orderStatusId');

        /** @var Order $model */
        $model = $this->modelManager->find(Order::class, $orderId);
        $this->setContext($orderId);

        // Only process valid models and payolution orders
        if ($model) {
            $this->executeOrderStateChange($model, $model->getOrderStatus()->getId(), $orderStatusId);
        }
    }

    /**
     * Execute the order state change process
     *
     * @param Order $order The order
     * @param null|int $currentStatusId The current status id
     * @param null|int $newOrderStateId The new order status id
     *
     * @return void
     */
    private function executeOrderStateChange(Order $order, $currentStatusId = null, $newOrderStateId = null)
    {
        if ($order->getPayment()->getAction() === 'PolPaymentPayolution') {
            $context = $this->stateChangeContextFactory->createFromOrder($order, $currentStatusId, $newOrderStateId);

            $changeMode = $this->orderStateChangeHandler->getOrderStateChangeMode(
                $context->getCurrentOrderStatusId(),
                $context->getNewOrderStatusId()
            );

            if ($changeMode === OrderStateChangeHandler::WHOLE_REFUND_MODE) {
                $changeMode = $this->orderStateChangeHandler->getCancelMode($order->getId());
            }

            $result = $this->orderStateChangeHandler->invokeAutomaticOrderProcess($order, $changeMode);

            if ($result) {
                //Flush capture/refund objects
                $this->modelManager->flush();
            }
        }
    }
}
