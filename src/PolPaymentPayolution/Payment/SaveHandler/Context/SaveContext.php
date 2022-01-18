<?php

namespace PolPaymentPayolution\Payment\SaveHandler\Context;

/**
 * Class SaveContext
 *
 * @package PolPaymentPayolution\Payment\SaveHandler\Context
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class SaveContext
{
    /**
     * Success state of save handler.
     *
     * @var bool
     */
    private $success;

    /**
     * Array of normalized payment data
     *
     * @var array
     */
    private $paymentData;

    /**
     * @var string|null
     */
    private $error;

    /**
     * SaveContext constructor.
     *
     * @param bool $success Success state of save handler
     * @param array $paymentData Array of normalized payment data
     * @param string|null $error
     */
    public function __construct($success, array $paymentData, $error = null)
    {
        $this->success = $success;
        $this->paymentData = $paymentData;
        $this->error = $error;
    }

    /**
     * Getter for success state of save handler.
     *
     * @return bool Returns success state of save handler
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Getter for normalized payment data.
     *
     * @return array Returns normalized payment data
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
