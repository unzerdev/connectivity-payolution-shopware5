<?php

namespace PolPaymentPayolution\Payment\Validator\Validators;

/**
 * Interface for the Validators
 *
 * @package PolPaymentPayolution\Payment\Validator\Validators
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
interface PaymentValidatorInterface
{
    /**
     * Validate Payment with saved variables
     *
     * @return bool
     */
    public function validate();

    /**
     * Checks if Validator supports payment
     *
     * @param string $payment
     *
     * @return bool
     */
    public function supports($payment);
}
