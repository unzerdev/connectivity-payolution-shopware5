<?php

namespace PolPaymentPayolution\Enum;

/**
 * Class OrderPosition
 *
 * @package PolPaymentPayolution\Enum
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
final class OrderPosition
{
    /**
     * Shipping identifier
     *
     * @var string
     */
    const SHIPPING_ID = 'invoice_shipping';

    /**
     * Difference price identifier
     *
     * @var string
     */
    const DIFFERENCE_PRINCE = 'invoice_difference';

    /**
     * Order refund identifier
     *
     * @var string
     */
    const ORDER_REFUND_IDENTIFIER = 'order_refund';

    /**
     * Shipping identifier
     *
     * @var string
     */
    const SHIPPING_IDENTIFIER = 1;

    /**
     * Difference identifier
     *
     * @var string
     */
    const DIFFERENCE_IDENTIFIER = 2;

    /**
     * OrderPosition constructor.
     */
    private function __construct()
    {
    }
}