<?php

namespace PolPaymentPayolution\State;

use Enlight_Controller_Front;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

/**
 * Factory to create the state change context based onto the request
 *
 * @package PolPaymentPayolution\State
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class StateChangeContextFactory
{
    /**
     * Text for missing request text
     *
     * @var string
     */
    const DEFAULT_IDENTIFIER_VALUE = 'unknown';

    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * The entity model manager
     *
     * @var ModelManager
     */
    private $modelManager;

    /**
     * StateChangeContextFactory constructor.
     *
     * @param Enlight_Controller_Front $front
     * @param ModelManager $modelManager
     */
    public function __construct(Enlight_Controller_Front $front, ModelManager $modelManager)
    {
        $this->front = $front;
        $this->modelManager = $modelManager;
    }

    /**
     * Create from order with optional StateChangeId
     *
     * @param Order $order
     * @param null|int $currentOrderStatusId The current order status
     * @param null|int $newOrderStatusId The new order status
     *
     * @return StateChangeContext
     */
    public function createFromOrder(Order $order, $currentOrderStatusId = null, $newOrderStatusId = null)
    {
        $controller = self::DEFAULT_IDENTIFIER_VALUE;
        $action = self::DEFAULT_IDENTIFIER_VALUE;
        $module = self::DEFAULT_IDENTIFIER_VALUE;

        if ($request = $this->front->Request()) {
            $controller = $request->getControllerName();
            $action = $request->getControllerName();
            $module = $request->getControllerName();
        }

        $entityChangeSet = [];
        if ($currentOrderStatusId === null || $newOrderStatusId === null) {
            $entityChangeSet = $this->modelManager->getUnitOfWork()->getEntityChangeSet($order);
        }

        return new StateChangeContext(
            $order,
            $controller,
            $action,
            $module,
            $currentOrderStatusId ?: $this->fetchOrderStateIdFromChangeSet($entityChangeSet),
            $newOrderStatusId ?: $this->fetchOrderStateIdFromChangeSet($entityChangeSet, false)
        );
    }

    /**
     * Fetch the current order state id
     *
     * @param array $entityChangeSet The changeset for the entity
     * @param bool $oldStatus Should the old status id from teh changeset be returned or the new value?
     *
     * @return int|null
     */
    private function fetchOrderStateIdFromChangeSet(array $entityChangeSet, $oldStatus = true)
    {
        $changeIndex = $oldStatus ? 0 : 1;

        $currentOrderState = isset($entityChangeSet['orderStatus'][$changeIndex])
            ? $entityChangeSet['orderStatus'][$changeIndex] :
            null;

        $currentOrderStateId = null;
        if ($currentOrderState instanceof Status) {
            $currentOrderStateId = $currentOrderState->getId();
        }

        return $currentOrderStateId;
    }
}
