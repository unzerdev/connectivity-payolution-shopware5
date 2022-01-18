<?php

namespace PolPaymentPayolution\Payment\Order;

/**
 * Class OrderPosition
 *
 * @package PolPaymentPayolution\Payment\Order
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class OrderPosition
{
    /**
     * @var OrderPositionIdentifier
     */
    private $identifier;

    /**
     * @var Amount
     */
    private $amount;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * OrderPosition constructor.
     *
     * @param OrderPositionIdentifier $identifier
     * @param Amount $amount
     * @param string $name
     * @param int $quantity
     */
    public function __construct(OrderPositionIdentifier $identifier, Amount $amount, $name, $quantity)
    {
        $this->identifier = $identifier;
        $this->amount = $amount;
        $this->name = $name;
        $this->quantity = $quantity;
    }

    /**
     * Get Identifier
     *
     * @return OrderPositionIdentifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get Amount
     *
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}