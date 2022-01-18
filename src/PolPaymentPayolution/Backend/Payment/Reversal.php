<?php

namespace PolPaymentPayolution\Backend\Payment;

use Exception;
use Payolution\Request\RequestWrapper;
use Payolution\Request\Reversal\CreatePostParams;
use Payolution\Request\Reversal\ReversalPayment;
use PolPaymentPayolution\Fetcher\OrderDataFetcher;

/**
 * Class Reversal
 *
 * @package PolPaymentPayolution\Backend\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Reversal
{
    /**
     * @var OrderDataFetcher
     */
    private $orderDataFetcher;

    /**
     * @var CreatePostParams
     */
    private $createPostParams;

    /**
     * @var RequestWrapper
     */
    private $requestWrapper;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var string
     */
    private $pluginVersion;

    /**
     * Reversal constructor.
     *
     * @param OrderDataFetcher $orderDataFetcher
     * @param CreatePostParams $createPostParams
     * @param RequestWrapper $requestWrapper
     * @param string $pluginName
     * @param string $pluginVersion
     */
    public function __construct(
        OrderDataFetcher $orderDataFetcher,
        CreatePostParams $createPostParams,
        RequestWrapper $requestWrapper,
        string $pluginName,
        string $pluginVersion
    ) {
        $this->orderDataFetcher = $orderDataFetcher;
        $this->createPostParams = $createPostParams;
        $this->requestWrapper = $requestWrapper;
        $this->pluginName = $pluginName;
        $this->pluginVersion = $pluginVersion;
    }

    /**
     * Execute request
     *
     * @param int $orderId
     * @param bool $amount
     *
     * @return array
     *
     * @throws Exception
     */
    public function Request($orderId, $amount = false)
    {
        $success = false;

        $orderData = $this->orderDataFetcher->fetchOrderData($orderId);

        $requestData = $this->createArray($orderData);

        $data = new ReversalPayment();

        foreach ($requestData as $method => $value) {
            $data->$method($value);
        }

        $post_data = $this->createPostParams->createParams($data);
        $response = $this->requestWrapper->doRequest($post_data);

        if ($response['PROCESSING_STATUS_CODE'] === '90' || $response['PROCESSING_STATUS_CODE'] === '00') {
            $success = true;
        }

        return array(
            'success' => $success,
            'post_data' => $post_data,
            'response' => $response,
        );
    }

    /**
     * Creates array for request
     *
     * @param $orderData
     * @return array
     * @throws Exception
     */
    public function createArray($orderData)
    {
        return array(
            'setREQUESTVERSION' => '1.0',
            'setTRANSACTIONRESPONSE' => 'SYNC',
            'setPAYMENTCODE' => 'VA.RV',
            'setIDENTIFICATIONREFERENCEID' => $orderData['referenceId'],
            'setPRESENTATIONUSAGE' => 'Invoice '.$orderData['invoiceId'],
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR' => 'Shopware_PHP_POST',
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION' => 'Shopware',
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE' => 'Webshop',
            'setIDENTIFICATIONTRANSACTIONID' => $orderData['ordernumber'],
            'setIDENTIFICATIONINVOICEID' => $orderData['invoiceId'],
            'setIDENTIFICATIONSHOPPERID' => $orderData['customernumber'],
            'setCRITERIONPAYOLUTIONMODULENAME' => $this->pluginName,
            'setCRITERIONPAYOLUTIONMODULEVERSION' => $this->pluginVersion,
            'setPAYOLUTIONPAYMENTMODE' => $orderData['payolutionMode'],
        );
    }
}