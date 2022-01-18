<?php
namespace Payolution\Request\Capture;

use Payolution\Config\AbstractConfig;

/**
 * Class Payolution_Request_Capture_CreatePostParams
 */
class CreatePostParams
{
    private $payolutionConfig;

    /**
     * class constructor
     *
     * @param AbstractConfig $payolutionConfig
     */
    public function __construct(AbstractConfig $payolutionConfig)
    {
        $this->payolutionConfig = $payolutionConfig;
    }

    /**
     * create Post Parameter for Payment
     *
     * @param $data
     * @return array
     */
    public function createParams(AbstractCapturePayment $data)
    {
        $post_data = array(
            'REQUEST.VERSION' => $data->getREQUESTVERSION(),
            'TRANSACTION.RESPONSE' => $data->getTRANSACTIONRESPONSE(), //Sync or Async
            'PAYMENT.CODE' => $data->getPAYMENTCODE(),
            'SECURITY.SENDER' => $this->payolutionConfig->getSender(),
            'USER.LOGIN' => $this->payolutionConfig->getLogin(),
            'USER.PWD' => $this->payolutionConfig->getPasswd(),
            'TRANSACTION.MODE' => 'LIVE',
            'IDENTIFICATION.REFERENCEID' => $data->getIDENTIFICATIONREFERENCEID(),
            'IDENTIFICATION.SHOPPERID' => $data->getIDENTIFICATIONSHOPPERID(),
            'PRESENTATION.USAGE' => $data->getPRESENTATIONUSAGE(),

            //system Data
            'CRITERION.PAYOLUTION_REQUEST_SYSTEM_VENDOR' => $data->getCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR(),
            'CRITERION.PAYOLUTION_REQUEST_SYSTEM_VERSION' => $data->getCRITERIONPAYOLUTIONMODULEVERSION(),
            'CRITERION.PAYOLUTION_REQUEST_SYSTEM_TYPE' => $data->getCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE(),

            'IDENTIFICATION.TRANSACTIONID' => $data->getIDENTIFICATIONTRANSACTIONID(),
            'IDENTIFICATION.INVOICEID' => $data->getIDENTIFICATIONINVOICEID(),
            'PRESENTATION.AMOUNT' => $data->getPRESENTATIONAMOUNT(),
            'PRESENTATION.CURRENCY' => $data->getPRESENTATIONCURRENCY(),
            'CRITERION.PAYOLUTION_TAX_AMOUNT' => $data->getCRITERIONPAYOLUTIONTAXAMOUNT(),
            'CRITERION.PAYOLUTION_MODULE_NAME' => $data->getCRITERIONPAYOLUTIONMODULENAME(),
            'CRITERION.PAYOLUTION_MODULE_VERSION' => $data->getCRITERIONPAYOLUTIONMODULEVERSION(),
            'CRITERION.PAYOLUTION_TRANSPORTATION_TRACKING' => $data->getCRITERIONPAYOLUTIONTRANSPORTATIONTRACKING(),
            'CRITERION.PAYOLUTION_TRANSPORTATION_RETURN_TRACKING' => $data->getCRITERIONPAYOLUTIONTRANSPORTATIONRETURNTRACKING(),
            'CRITERION.PAYOLUTION_TRANSPORTATION_COMPANY' => $data->getCRITERIONPAYOLUTIONTRANSPORTATIONCOMPANY(),
            'CRITERION.PAYOLUTION_ORDER_ID' => $data->getCRITERIONPAYOLUTIONORDERID(),
            'CRITERION.PAYOLUTION_INVOICE_ID' => $data->getCRITERIONPAYOLUTIONINVOICEID(),
            'CRITERION.PAYOLUTION_CUSTOMER_NUMBER' => $data->getCRITERIONPAYOLUTIONCUSTOMERNUMBER()

        );

        if ($this->payolutionConfig->isTestmode()) {
            $post_data['TRANSACTION.MODE'] = 'CONNECTOR_TEST';
        }

        switch ($data->getPAYOLUTIONPAYMENTMODE()) {
            case 'PAYOLUTION_INVOICE':
                $transactionChannel = $this->payolutionConfig->getChannelInvoice();
                break;
            case 'PAYOLUTION_INVOICE_B2B':
                $transactionChannel = $this->payolutionConfig->getChannelB2bInvoice();
                break;
            case 'PAYOLUTION_INS':
            case 'PAYOLUTION_INSTALLMENT':
            $transactionChannel = $this->payolutionConfig->getChannelInstallment();
                break;
            case 'PAYOLUTION_ELV':
                $transactionChannel = $this->payolutionConfig->getChannelElv();
                break;
            default:
                $transactionChannel = $this->payolutionConfig->getChannelInvoice();
        }

        $post_data['TRANSACTION.CHANNEL'] = $transactionChannel;

        return $post_data;
    }
}
