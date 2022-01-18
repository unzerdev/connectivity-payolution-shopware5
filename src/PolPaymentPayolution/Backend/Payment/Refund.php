<?php

namespace PolPaymentPayolution\Backend\Payment;

use Payolution\Request\Refund\CreatePostParams;
use Payolution\Request\Refund\RefundPayment;
use Payolution\Request\RequestWrapper;
use PolPaymentPayolution\Backend\Data\Order;
use PolPaymentPayolution\Config\ConfigProvider;
use PolPaymentPayolution\Fetcher\OrderDataFetcher;
use Zend_Db_Adapter_Abstract as Zend;

/**
 * Class Refund
 *
 * @package PolPaymentPayolution\Backend\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Refund
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
     * @var Zend
     */
    private $db;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var string
     */
    private $pluginVersion;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Refund constructor.
     *
     * @param Zend $db
     * @param OrderDataFetcher $orderDataFetcher
     * @param CreatePostParams $createPostParams
     * @param RequestWrapper $requestWrapper
     * @param ConfigProvider $configProvider
     * @param string $pluginName
     * @param string $pluginVersion
     */
    public function __construct(
        Zend $db,
        OrderDataFetcher $orderDataFetcher,
        CreatePostParams $createPostParams,
        RequestWrapper $requestWrapper,
        ConfigProvider $configProvider,
        $pluginName,
        $pluginVersion
    ) {
        $this->db = $db;
        $this->orderDataFetcher = $orderDataFetcher;
        $this->createPostParams = $createPostParams;
        $this->requestWrapper = $requestWrapper;
        $this->configProvider = $configProvider;
        $this->pluginName = $pluginName;
        $this->pluginVersion = $pluginVersion;
    }

    /**
     * Execute Request
     *
     * @param int $orderId
     * @param float $absolute
     * @param bool $percentage
     * @param bool $positions
     *
     * @return array
     */
    public function Request($orderId, $absolute, $percentage = false, $positions = false)
    {
        $success = false;
        $post_data = [];
        $response = [];

        $orderData = $this->orderDataFetcher->fetchOrderData($orderId);

        $restAmount = round($orderData['capturedAmount'] - $orderData['refundedAmount'],2);

            if(!empty($absolute)) {
                $orderData['amount'] = round($absolute,2);
                $orderData['taxAmount'] = round($absolute - ($absolute / $orderData['taxRate']),2);
            } elseif (!empty($percentage) && $percentage <= 100) {
                $orderData['amount'] = round($orderData['amount'] * ($percentage / 100),2);
                $orderData['taxAmount'] = round($orderData['amount'] - ($orderData['amount'] / $orderData['taxRate']),2);
            }

            if($restAmount < $orderData['amount']) {
                return [
                    'success' => $success,
                    'post_data' => $post_data,
                    'response' => $response,
                ];
            }

            $requestData = $this->createArray($orderData);
            $data = new RefundPayment();

            foreach ($requestData as $method => $value) {
                $data->$method($value);
            }

            $post_data = $this->createPostParams->createParams($data);
            $response = $this->requestWrapper->doRequest($post_data);

            if ($response['PROCESSING_STATUS_CODE'] == '90' || $response['PROCESSING_STATUS_CODE'] == '00') {
                if ($positions !== false) {
                    $setPositionData = '
                        UPDATE
                          s_order_details_attributes
                        SET
                          payolution_refund = (payolution_refund + :quantity)
                        WHERE
                          detailID = :positionId';
                    foreach ($positions as $position) {
                        if($position->id != NULL) {
                            $this->db->query(
                                $setPositionData,
                                array(
                                    ':quantity' => $position->quantity,
                                    ':positionId' => $position->id
                                )
                            );
                        }
                    }
                }

                $this->db->query('
                        UPDATE
                          s_order_attributes
                        SET
                          payolution_refund = ROUND(payolution_refund + :amount,2)
                        WHERE
                          orderID = :orderId',
                    array(
                        ':amount' => $orderData['amount'],
                        ':orderId' => $orderId
                    )
                );

                $config = $this->configProvider->getConfigByOrderId($orderId);

                switch ($orderData['payolutionMode']) {
                    case 'PAYOLUTION_INS':
                        $orderstate = $config->getOrderStateInstallment();
                        break;
                    case 'PAYOLUTION_INVOICE':
                        $orderstate = $config->getRefundOrderStateB2C();
                        break;
                    case 'PAYOLUTION_ELV':
                        $orderstate = $config->getRefundOrderStateELV();
                        break;
                    case 'PAYOLUTION_INVOICE_B2B':
                        $orderstate = $config->getRefundOrderStateB2B();
                        break;
                }

                $this->db->query('
                        UPDATE
                          s_order
                        SET
                          cleared = :cleared
                        WHERE
                          id = :orderId',
                    array(
                        ':cleared' => $orderstate,
                        ':orderId' => $orderId
                    )
                );
                $success = true;
            }

        return [
            'success' => $success,
            'post_data' => $post_data,
            'response' => $response,
        ];
    }

    /**
     * Get refund data for order backend.
     *
     * @param int $orderId
     * @return array
     */
    public static function getRefundDataForOrderBackend($orderId)
    {
        return Order::getOrderPositions($orderId, 'refund');
    }

    /**
     * Create array for request.
     *
     * @param array $orderData
     * @return array
     */
    public function createArray($orderData)
    {
        return [
            'setREQUESTVERSION' => '1.0',
            'setTRANSACTIONRESPONSE' => 'SYNC',
            'setPAYMENTCODE' => 'VA.RF',
            'setIDENTIFICATIONREFERENCEID' => $orderData['referenceId'],
            'setPRESENTATIONUSAGE' => 'Invoice '.$orderData['invoiceId'],
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR' => 'Shopware_PHP_POST',
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION' => 'Shopware',
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE' => 'Webshop',
            'setIDENTIFICATIONTRANSACTIONID' => $orderData['ordernumber'],
            'setIDENTIFICATIONINVOICEID' => $orderData['invoiceId'],
            'setPRESENTATIONAMOUNT' => $orderData['amount'],
            'setPRESENTATIONCURRENCY' => $orderData['currency'],
            'setCRITERIONPAYOLUTIONTAXAMOUNT' => $orderData['taxAmount'],
            'setCRITERIONPAYOLUTIONMODULENAME' => $this->pluginName,
            'setCRITERIONPAYOLUTIONMODULEVERSION' => $this->pluginVersion,
            'setPAYOLUTIONPAYMENTMODE' => $orderData['payolutionMode'],
        ];
    }
}