<?php

namespace PolPaymentPayolution\Payment\Order;

/**
 * Class Amount
 *
 * @package PolPaymentPayolution\Payment\Order
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Amount
{
    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $currency;

    /**
     * Amount constructor.
     *
     * @param float $value
     * @param string $currency
     */
    public function __construct($value, $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * Get Value
     *
     * @return float
     */
    public function getValue()
    {
        return round($this->value,2);
    }

    /**
     * Get Currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get Normalized Amount
     *
     * @return string
     */
    public function getNormalizedAmount()
    {
        return sprintf('%s %s', $this->value, $this->currency);
    }
}
