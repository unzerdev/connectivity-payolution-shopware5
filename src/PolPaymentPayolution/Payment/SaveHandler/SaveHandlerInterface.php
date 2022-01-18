<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use PolPaymentPayolution\Payment\SaveHandler\Context\SaveContext;
use PolPaymentPayolution\Payment\SaveHandler\Context\ValidationContext;

/**
 * Class SaveHandlerInterface
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
interface SaveHandlerInterface
{
    /**
     * Saves the given payment data
     *
     * @param array $paymentData Array of payment data send by the form
     *
     * @return SaveContext Returns SaveContext containing success state and normalized payment data
     */
    public function save(array $paymentData);

    /**
     * Validates the given payment data.
     *
     * @param array $paymentData Array of payment data send by the form
     *
     * @return ValidationContext Returns a validation context with success state and paymentData
     */
    public function validate(array &$paymentData);

    /**
     * Check if Handler Supports payment Type
     *
     * @param $shortCut
     * @return bool
     */
    public function supports($shortCut);
}
