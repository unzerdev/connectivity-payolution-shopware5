<?php

namespace PolPaymentPayolution\Event;

use Enlight_Event_EventArgs;
use PolPaymentPayolution\Payment\Capture\CapturePosition;
use PolPaymentPayolution\Payment\Order\PositionCollection;
use Shopware\Models\Order\Order;

/**
 * Class PositionCreateEventy
 *
 * @package PolPaymentPayolution\Event
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PositionCreateEvent extends Enlight_Event_EventArgs
{
    /**
     * Event Name
     *
     * @var string
     */
    const EVENT_NAME = 'payolution.position.create';

    /**
     * @var Order
     */
    private $order;

    /**
     * @var PositionCollection
     */
    private $positions;

    /**
     * @var string
     */
    private $type;

    /**
     * CapturePositionCreateEvent constructor.
     *
     * @param Order $order
     * @param PositionCollection $positions
     * @param string $type
     */
    public function __construct(Order $order, PositionCollection $positions, $type)
    {
        $this->order = $order;
        $this->positions = $positions;
        $this->type = $type;

        parent::__construct();
    }

    /**
     * Get Order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get Positions
     *
     * @return PositionCollection
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}