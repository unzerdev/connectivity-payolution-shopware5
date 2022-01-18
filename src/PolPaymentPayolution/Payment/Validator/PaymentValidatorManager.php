<?php

namespace PolPaymentPayolution\Payment\Validator;

use PolPaymentPayolution\Payment\Validator\Validators\PaymentValidatorInterface;

/**
 * Manager to handle the validations for the payments
 *
 * @package PolPaymentPayolution\Payment\Validator
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PaymentValidatorManager
{
    /**
     * @var PaymentValidatorInterface[]
     */
    private $validators;

    /**
     * Add Validator
     *
     * @param PaymentValidatorInterface $paymentValidator
     *
     * @return void
     */
    public function addValidator(PaymentValidatorInterface $paymentValidator)
    {
        $this->validators[] = $paymentValidator;
    }

    /**
     * Validate given Payment
     *
     * @param string $payment
     *
     * @return bool
     */
    public function validate($payment)
    {
        $result = true;

        foreach ($this->validators as $validator) {
            if ($validator->supports($payment)) {
                $result = $validator->validate();

                break;
            }
        }

        return $result;
    }
}
