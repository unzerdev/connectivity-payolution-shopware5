<?php

namespace Payolution\Request\Builder\Mapper;

use Payolution\Config\Config;
use PolPaymentPayolution\Fetcher\OrderDataFetcher;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class WorkflowMapper
 *
 * @package Payolution\Request\Builder\Mapper
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowMapper
{
    /**
     * @var OrderDataFetcher
     */
    private $orderDataFetcher;

    /**
     * @var Config
     */
    private $config;

    /**
     * WorkflowMapper constructor.
     *
     * @param OrderDataFetcher $orderDataFetcher
     * @param Config $config
     */
    public function __construct(OrderDataFetcher $orderDataFetcher, Config $config)
    {
        $this->orderDataFetcher = $orderDataFetcher;
        $this->config = $config;
    }

    /**
     * Map Request
     *
     * @param int $orderId
     * @param float $amount
     * @param array $request
     *
     * @return void
     */
    public function mapRequest($orderId, $amount, array &$request)
    {
        $invoiceIdentifier = 'Invoice ';
        $orderData = $this->orderDataFetcher->fetchOrderData($orderId);
        $request['IDENTIFICATION.REFERENCEID'] = $orderData['referenceId'];
        $request['IDENTIFICATION.SHOPPERID'] = $orderData['customernumber'];
        $request['IDENTIFICATION.TRANSACTIONID'] = $orderData['ordernumber'];
        $request['IDENTIFICATION.INVOICEID'] = $orderData['invoiceId'];
        $request['PRESENTATION.USAGE'] =  $invoiceIdentifier .$orderData['invoiceId'];
        $request['PRESENTATION.AMOUNT'] = $amount;
        $request['PRESENTATION.CURRENCY'] = $orderData['currency'];
        $request['CRITERION.PAYOLUTION_TAX_AMOUNT'] = $orderData['taxAmount'];
        $request['CRITERION.PAYOLUTION_ORDER_ID'] = $orderData['ordernumber'];
        $request['CRITERION.PAYOLUTION_INVOICE_ID'] = $orderData['invoiceId'];
        $request['CRITERION.CUSTOMER_NUMBER'] = $orderData['customernumber'];
        $request['CRITERION.PAYOLUTION_TRANSPORTATION_TRACKING'] = $orderData['trackingcode'];
        $request['CRITERION.PAYOLUTION_TRANSPORTATION_RETURN_TRACKING'] = '';
        $request['CRITERION.PAYOLUTION_TRANSPORTATION_COMPANY'] = '';
        $request['TRANSACTION.CHANNEL'] = $this->getTransactionChannel($orderData['payolutionMode']);
    }

    /**
     * Get TransactionChannel
     *
     * @param string $paymentType
     *
     * @return string
     */
    private function getTransactionChannel($paymentType)
    {
        switch ($paymentType) {
            case 'PAYOLUTION_INVOICE':
                $transactionChannel = $this->config->getChannelInvoice();
                break;
            case 'PAYOLUTION_INVOICE_B2B':
                $transactionChannel = $this->config->getChannelB2bInvoice();
                break;
            case 'PAYOLUTION_INS':
            case 'PAYOLUTION_INSTALLMENT':
                $transactionChannel = $this->config->getChannelInstallment();
                break;
            case 'PAYOLUTION_ELV':
                $transactionChannel = $this->config->getChannelElv();
                break;
            default:
                $transactionChannel = $this->config->getChannelInvoice();
        }

        return $transactionChannel;
    }
}