<?php

namespace PolPaymentPayolution\Payment\Order;

use Doctrine\ORM\ORMException;
use Enlight_Event_EventManager;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Enum\PaymentType;
use PolPaymentPayolution\Event\PositionCreateEvent;
use PolPaymentPayolution\Payment\Factory\CapturePositionsFactory;
use PolPaymentPayolution\Payment\Order\Factory\PositionFactory;
use Shopware\Models\Order\Order;

/**
 * Class PositionProvider
 *
 * @package PolPaymentPayolution\Payment\Order
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PositionProvider
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var PositionFactory
     */
    private $positionsFactory;

    /**
     * @var Enlight_Event_EventManager
     */
    private $eventDispatcher;

    /**
     * PositionProvider constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param PositionFactory $positionsFactory
     * @param Enlight_Event_EventManager $eventDispatcher
     */
    public function __construct(
        ComponentManagerInterface $componentManager,
        PositionFactory $positionsFactory,
        Enlight_Event_EventManager $eventDispatcher
    ) {
        $this->componentManager = $componentManager;
        $this->positionsFactory = $positionsFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get Capture Collection
     *
     * @param int $orderId
     *
     * @return PositionCollection
     */
    public function getCaptureCollection($orderId)
    {
        if (!$order = $this->getOrderById($orderId)) {
            return new PositionCollection([]);
        }

        $type =  PaymentType::CAPTURE;

        return $this->createCollection(
            $this->positionsFactory->createPosition($order, $type),
            $order,
            $type
        );
    }

    /**
     * Get Refund Collection
     *
     * @param int $orderId
     *
     * @return PositionCollection
     */
    public function getRefundCollection($orderId)
    {
        if (!$order = $this->getOrderById($orderId)) {
            return new PositionCollection([]);
        }

        $type =  PaymentType::REFUND;

        return $this->createCollection(
            $this->positionsFactory->createPosition($order, $type),
            $order,
            $type
        );
    }

    /**
     * Create Collection
     *
     * @param array $positions
     * @param Order $order
     * @param string $type
     *
     * @return PositionCollection
     */
    private function createCollection(array $positions, Order $order, $type)
    {
        $collection = new PositionCollection($positions);

        $event = new PositionCreateEvent($order, $collection, $type);

        /**
         * @var PositionCreateEvent $event
         */
        $event = $this->eventDispatcher->filter(PositionCreateEvent::EVENT_NAME, $event);

        return $event->getPositions();
    }

    /**
     * Get Order By ID
     *
     * @param int $orderId
     *
     * @return null|Order
     */
    private function getOrderById($orderId)
    {
        try {
            $order = $this->componentManager->getModelManager()->find(Order::class, $orderId);
        } catch (ORMException $e) {
            return null;
        }

        if (!$order) {
            return null;
        }

        return $order;
    }
}