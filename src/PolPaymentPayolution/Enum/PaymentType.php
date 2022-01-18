<?php

namespace PolPaymentPayolution\Enum;

/**
 * Class PaymentType
 *
 * @package PolPaymentPayolution\Enum
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
final class PaymentType
{
    /**
     * Capture Key
     *
     * @var string
     */
    const CAPTURE = 'capture';

    /**
     * Refund Key
     *
     * @var string
     */
    const REFUND = 'refund';

    /**
     * Reversal Key
     *
     * @var string
     */
    const REVERSAL = 'reversal';

    /**
     * B2C
     *
     * @var string
     */
    const B2C_PAYMENT = 'payolution_invoice_b2c';

    /**
     * B2B
     *
     * @var string
     */
    const B2B_PAYMENT = 'payolution_invoice_b2b';

    /**
     * Installment
     *
     * @var string
     */
    const INSTALLMENT_PAYMENT = 'payolution_installment';

    /**
     * ELV
     *
     * @var string
     */
    const ELV_PAYMENT = 'payolution_elv';

    /**
     * PaymentType constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get Payolution Payment Types
     *
     * @return array
     */
    public static function getPaymentTypes()
    {
        return [
            self::B2B_PAYMENT,
            self::B2C_PAYMENT,
            self::ELV_PAYMENT,
            self::INSTALLMENT_PAYMENT
        ];
    }
}