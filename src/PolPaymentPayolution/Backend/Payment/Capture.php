<?php
namespace PolPaymentPayolution\Backend\Payment;

use Payolution\Request\Capture\CapturePayment;
use Payolution\Request\Capture\CreatePostParams;
use Payolution\Request\RequestWrapper;
use PolPaymentPayolution\Backend\Data\Order;
use PolPaymentPayolution\Config\ConfigProvider;
use PolPaymentPayolution\Fetcher\OrderDataFetcher;
use Zend_Db_Adapter_Abstract as Zend;

/**
 * Class Capture
 *
 * @package PolPaymentPayolution\Backend\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Capture
{
    /**
     * @var Zend
     */
    private $db;

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
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Capture constructor.
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
     * @param bool $captureRestAmount
     *
     * @return array
     */
    public function Request($orderId, $absolute, $percentage = false, $positions = false, $captureRestAmount = false)
    {
        $success = false;
        $post_data = array();
        $response = array();

        $orderData = $this->orderDataFetcher->fetchOrderData($orderId);

        $restAmount = round($orderData['amount'] - $orderData['capturedAmount'], 2);

        if ($restAmount >= $absolute || $restAmount != 0) {
            if (!empty($absolute)) {
                $orderData['amount'] = round($absolute, 2);
                $orderData['taxAmount'] = round($absolute - ($absolute / $orderData['taxRate']), 2);
            } elseif (!empty($percentage) && $percentage <= 100) {
                if ($captureRestAmount === true) {
                    $orderData['amount'] = round($restAmount * ($percentage / 100), 2);
                    $orderData['taxAmount'] = round($orderData['amount'] - ($orderData['amount']
                            / $orderData['taxRate']), 2);
                } else {
                    $orderData['amount'] = round($orderData['amount'] * ($percentage / 100), 2);
                    $orderData['taxAmount'] = round($orderData['amount'] - ($orderData['amount']
                            / $orderData['taxRate']), 2);
                }
            }

            if ($restAmount < $orderData['amount']) {
                return [
                    'success' => $success,
                    'post_data' => $post_data,
                    'response' => $response,
                ];
            }

            $requestData = $this->createArray($orderData);
            $data = new CapturePayment();

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
                          payolution_capture = (payolution_capture + :quantity)
                        WHERE
                          detailID = :positionId';
                    foreach ($positions as $position) {
                        if ($position->id != null) {
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
                $this->db->query(
                    'UPDATE
                      s_order_attributes
                    SET
                      payolution_capture = ROUND(payolution_capture + :amount,2)
                    WHERE
                      orderID = :orderId',
                    array(
                        ':amount' => $orderData['amount'],
                        ':orderId' => $orderId
                    )
                );

                $config = $this->configProvider->getConfigByOrderId($orderId);

                switch ($orderData['payolutionMode']) {
                    case 'PAYOLUTION_INVOICE':
                        $orderstate = $config->getCaptureOrderStateB2C();
                        break;
                    case 'PAYOLUTION_INVOICE_B2B':
                        $orderstate = $config->getCaptureOrderStateB2B();
                        break;
                    case 'PAYOLUTION_ELV':
                        $orderstate = $config->getCaptureOrderStateELV();
                        break;
                    case 'PAYOLUTION_INS':
                        $orderstate = $config->getOrderStateInstallment();
                        break;
                }

                $this->db->query(
                    'UPDATE
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
        }

        return [
            'success' => $success,
            'post_data' => $post_data,
            'response' => $response,
        ];
    }

    /**
     * Get capture data for order backend.
     *
     * @param int $orderId
     * @return array
     */
    public static function getCaptureDataForOrderBackend($orderId)
    {
        return Order::getOrderPositions($orderId, 'capture');
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
            'setPAYMENTCODE' => 'VA.CP',
            'setIDENTIFICATIONREFERENCEID' => $orderData['referenceId'],
            'setIDENTIFICATIONSHOPPERID' => $orderData['customernumber'],
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
            'setCRITERIONPAYOLUTIONTRANSPORTATIONTRACKING' => $orderData['trackingcode'],
            'setCRITERIONPAYOLUTIONTRANSPORTATIONRETURNTRACKING' => '',
            'setCRITERIONPAYOLUTIONTRANSPORTATIONCOMPANY' => '',
            'setCRITERIONPAYOLUTIONORDERID' => $orderData['ordernumber'],
            'setCRITERIONPAYOLUTIONINVOICEID' => $orderData['invoiceId'],
            'setCRITERIONPAYOLUTIONCUSTOMERNUMBER' => $orderData['customernumber'],
            'setPAYOLUTIONPAYMENTMODE' => $orderData['payolutionMode'],
        ];
    }
}