<?php

namespace PolPaymentPayolution\Config;

use Shopware\Models\Shop\Shop;

/**
 * Class that holds the whole config
 *
 * @package PolPaymentPayolution\Config
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Config
{
    /**
     * The array with all config parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * Config constructor.
     *
     * @param array $parameters
     * @param Shop $shop
     */
    public function __construct(array $parameters, Shop $shop)
    {
        $this->parameters = $parameters;
        $this->shop = $shop;
    }

    /**
     * Get Shop
     *
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getInitialOrderStateB2C()
    {
        return $this->parameters['b2cOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderStateB2C()
    {
        return $this->parameters['b2cOrderStateCapture'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateB2C()
    {
        return $this->parameters['b2cOrderStateRefund'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getInitialOrderStateB2B()
    {
        return $this->parameters['b2bOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderStateB2B()
    {
        return $this->parameters['b2bOrderStateCapture'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateB2B()
    {
        return $this->parameters['b2bOrderStateRefund'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getOrderStateInstallment()
    {
        return $this->parameters['installmentOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateInstallment()
    {
        return $this->parameters['installmentOrderStateRefund'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getInitialOrderStateELV()
    {
        return $this->parameters['elvOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderStateELV()
    {
        return $this->parameters['elvOrderStateCapture'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderStateELV()
    {
        return $this->parameters['elvOrderStateRefund'];
    }

    /**
     * Is Order Automatic Refund after delete active
     *
     * @return bool
     */
    public function isAutomaticOrderRefund()
    {
        return (bool) $this->parameters['automaticRefundOrder'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getRefundOrderState()
    {
        return $this->parameters['refundOrderState'];
    }

    /**
     * Get Initial Order State b2c
     *
     * @return int
     */
    public function getCaptureOrderState()
    {
        return $this->parameters['captureOrderState'];
    }

    /**
     * Is the automatic order capture active?
     *
     * @return bool
     */
    public function isAutomaticCaptureOrders()
    {
        return (bool) $this->parameters['automaticCaptureAfterOrder'];
    }

    /**
     * Is Automatic Order refund active
     *
     * @return bool
     */
    public function isAutomaticRefundCancellationPositions()
    {
        return (bool) $this->parameters['automaticRefundPositionsPickware'];
    }

    /**
     * Is History Simple View
     *
     * @return bool
     */
    public function isHistorySimpleView()
    {
        return (bool) $this->parameters['simpleHistoryMessages'];
    }

    /**
     * Is the testing mode active ?
     *
     * @return bool
     */
    public function isTestmode()
    {
        return  filter_var($this->parameters['testmode'], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Are different addresses (billing <-> shipping allowed) ?
     *
     * @return bool
     */
    public function isDifferentAddresses()
    {
        return  filter_var($this->parameters['differentAddresses'], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Is the logging active?
     *
     * @return bool
     */
    public function isLogging()
    {
        return  filter_var($this->parameters['logging'], FILTER_VALIDATE_BOOLEAN);
    }


    /**
     * Get the login name from the credentials
     *
     * @return string
     */
    public function getLogin()
    {
        return  $this->parameters['login'];
    }

    /**
     * Get the sender from the credentials
     *
     * @return string
     */
    public function getSender()
    {
        return  $this->parameters['sender'];
    }

    /**
     * Get the password from the credentials
     *
     * @return string
     */
    public function getPasswd()
    {
        return  $this->parameters['passwd'];
    }

    /**
     * Get the user name for the installment
     *
     * @return string
     */
    public function getInstallmentPayolutionUser()
    {
        return $this->parameters['installment_payolution_user'];
    }

    /**
     * Get the user password for the installment
     *
     * @return string
     */
    public function getInstallmentPayolutionPassword()
    {
        return $this->parameters['installment_payolution_password'];
    }

    /**
     * Get the bcc mail address
     *
     * @return string
     */
    public function getMailBcc()
    {
        return  $this->parameters['mail_bcc'];
    }

    /**
     * Get the payolution Iban for the invoices
     *
     * @return string
     */
    public function getIban()
    {
        return  $this->parameters['iban'];
    }

    /**
     * Get the payolution Bic for the invoices
     *
     * @return string
     */
    public function getBic()
    {
        return  $this->parameters['bic'];
    }

    /**
     * Get the payolution holder for the invoices
     *
     * @return string
     */
    public function getHolder()
    {
        return $this->parameters['holder'];
    }

    /**
     * Get the company for the invoices
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->parameters['company'];
    }

    /**
     * Get the channel for the payment B2C invoice
     *
     * @return string
     */
    public function getChannelInvoice()
    {
        return  $this->parameters['channel_invoice'];
    }

    /**
     * Get the channel for the payment B2B invoice
     *
     * @return string
     */
    public function getChannelB2bInvoice()
    {
        return  $this->parameters['channel_b2b_invoice'];
    }

    /**
     * Get the channel for the payment installment
     *
     * @return string
     */
    public function getChannelInstallment()
    {
        return  $this->parameters['channel_installment'];
    }

    /**
     * Get the channel for the payment elv
     *
     * @return string
     */
    public function getChannelElv()
    {
        return  $this->parameters['channel_elv'];
    }

    /**
     * Get the channel for the payment precheck
     *
     * @return string
     */
    public function getChannelPrecheck()
    {
        return  $this->parameters['channel_precheck'];
    }

    /**
     * Get the min basket value for the payment installment
     *
     * @return float
     */
    public function getMinInstallmentValue()
    {
        return (float) $this->parameters['min_installment_value'];
    }

    /**
     * Get the max basket value for the payment installment
     *
     * @return float
     */
    public function getMaxInstallmentValue()
    {
        return (float) $this->parameters['max_installment_value'];
    }

    /**
     * Get the min basket value for the payment b2b invoice
     *
     * @return float
     */
    public function getMinB2bValue()
    {
        return (float) $this->parameters['min_b2b_value'];
    }

    /**
     * Get the max basket value for the payment b2b invoice
     *
     * @return float
     */
    public function getMaxB2bValue()
    {
        return (float) $this->parameters['max_b2b_value'];
    }

    /**
     * Get the min basket value for the payment b2c invoice
     *
     * @return float
     */
    public function getMinB2cValue()
    {
        return (float) $this->parameters['min_b2c_value'];
    }

    /**
     * Get the max basket value for the payment b2c invoice
     *
     * @return float
     */
    public function getMaxB2cValue()
    {
        return (float) $this->parameters['max_b2c_value'];
    }

    /**
     * Get the min basket value for the payment elv
     *
     * @return float
     */
    public function getMinElvValue()
    {
        return (float) $this->parameters['min_elv_value'];
    }

    /**
     * Get the max basket value for the payment elv
     *
     * @return float
     */
    public function getMaxElvValue()
    {
        return (float) $this->parameters['max_elv_value'];
    }

    /**
     * Get the min article price installment display on the detail page
     *
     * @return float
     */
    public function getMinInstallmentDetailValue()
    {
        return (float) $this->parameters['min_installment_detail_value'];
    }

    /**
     * Get the max article price installment display on the detail page
     *
     * @return float
     */
    public function getMaxInstallmentDetailValue()
    {
        return (float) $this->parameters['max_installment_detail_value'];
    }

    /**
     * The the allowed countries for the payment b2c invoice
     *
     * @return string[]
     */
    public function getAllowedCountriesInvoiceB2C()
    {
        return array_map('trim', explode(',', $this->parameters['allowedCountriesInvoiceB2C']));
    }

    /**
     * The the allowed countries for the payment b2c invoice
     *
     * @return string[]
     */
    public function getAllowedCountriesInvoiceB2B()
    {
        return array_map('trim', explode(',', $this->parameters['allowedCountriesInvoiceB2B']));
    }

    /**
     * The the allowed countries for the payment b2c invoice
     *
     * @return string[]
     */
    public function getAllowedCountriesInstallment()
    {
        return array_map('trim', explode(',', $this->parameters['allowedCountriesInstallment']));
    }

    /**
     * The the allowed countries for the payment b2c invoice
     *
     * @return string[]
     */
    public function getAllowedCountriesElv()
    {
        return array_map('trim', explode(',', $this->parameters['allowedCountriesElv']));
    }

    /**
     * The the allowed currencies
     *
     * @return string[]
     */
    public function getAllowedCurrencies()
    {
        return array_map('trim', explode(',', $this->parameters['allowedCurrencies']));
    }
}