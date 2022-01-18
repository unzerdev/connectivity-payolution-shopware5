<?php

namespace PolPaymentPayolution\Payment;

/**
 * Class PayolutionShipping
 *
 * @package PolPaymentPayolution\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PayolutionShipping
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var int
     */
    private $quantity;

    /**
     * PayolutionShipping constructor.
     *
     * @param float $amount
     * @param int $quantity
     */
    public function __construct($amount, $quantity)
    {
        $this->amount = $amount;
        $this->quantity = $quantity;
    }

    /**
     * Get Amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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