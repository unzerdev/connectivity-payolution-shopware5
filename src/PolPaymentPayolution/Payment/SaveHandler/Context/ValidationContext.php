<?php

namespace PolPaymentPayolution\Payment\SaveHandler\Context;

/**
 * Class ValidationContext
 *
 * @package PolPaymentPayolution\Payment\SaveHandler\Context
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ValidationContext
{
    /**
     * Holds success state of SaveHandler validation.
     *
     * @var bool
     */
    private $success;

    /**
     * Holds normalized paymentData of SaveHandler validation.
     *
     * @var array
     */
    private $paymentData;

    /**
     * @var string|null
     */
    private $error;

    /**
     * ValidationContext constructor.
     *
     * @param bool $success Success state of validation
     * @param array $paymentData Normalized paymentData
     * @param string|null $error
     */
    public function __construct($success, array $paymentData, $error = null)
    {
        $this->success = $success;
        $this->paymentData = $paymentData;
        $this->error = $error;
    }

    /**
     * Returns validation success state.
     *
     * @return bool Returns true if validation is successful or false
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Returns (normalized) paymentData.
     *
     * @return array If validation is successful it returns normalized paymentData or non normalized paymentData
     */
    public function getPaymentData()
    {
        return $this->paymentData;
    }

    /**
     * Get Error
     *
     * @return null|string
     */
    public function getError()
    {
        return $this->error;
    }
}
