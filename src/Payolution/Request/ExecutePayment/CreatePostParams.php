<?php
namespace Payolution\Request\ExecutePayment;

use Payolution\Config\AbstractConfig;

/**
 * Class Payolution_Request_ExecutePayment_CreatePostParams
 */
class CreatePostParams
{


    /**
     * class constructor
     *
     */
    public function __construct()
    {

    }

    /**
     * create Post Parameter for Payment
     *
     * @param $data
     * @param $payolutionConfig
     * @return array
     */
    public static function createParams(AbstractExecutePayment $data, AbstractConfig $payolutionConfig)
    {
        $post_data = array(
            'REQUEST.VERSION' => $data->getREQUESTVERSION(),
            'TRANSACTION.RESPONSE' => $data->getTRANSACTIONRESPONSE(), //Sync or Async
            'PAYMENT.CODE' => $data->getPAYMENTCODE(),
            'ACCOUNT.BRAND' => $data->getACCOUNTBRAND(),
            'SECURITY.SENDER' => $payolutionConfig->getSender(),
            'USER.LOGIN' => $payolutionConfig->getLogin(),
            'USER.PWD' => $payolutionConfig->getPasswd(),
            'TRANSACTION.MODE' => 'LIVE',
            'TRANSACTION.CHANNEL' => $payolutionConfig->getChannelInvoice(),
            'IDENTIFICATION.TRANSACTIONID' => $data->getIDENTIFICATIONTRANSACTIONID(),
            'IDENTIFICATION.SHOPPERID' => $data->getIDENTIFICATIONSHOPPERID(),
            'PRESENTATION.AMOUNT' =>  $data->getPRESENTATIONAMOUNT(),
            'PRESENTATION.CURRENCY' => $data->getPRESENTATIONCURRENCY(),
            'PRESENTATION.USAGE' => $data->getPRESENTATIONUSAGE(),
            'NAME.SEX' => $data->getNAMESEX(),
            'NAME.GIVEN' => $data->getNAMEGIVEN(),
            'NAME.FAMILY' => $data->getNAMEFAMILY(),
            'NAME.BIRTHDATE' => $data->getNAMEBIRTHDATE(),
            'CONTACT.EMAIL' => $data->getCONTACTEMAIL(),
            'CONTACT.PHONE'  => $data->getCONTACTPHONE(),
            'CONTACT.IP' => $data->getCONTACTIP(),
            'ADDRESS.STREET' => $data->getADDRESSSTREET(),
            'ADDRESS.ZIP'  => $data->getADDRESSZIP(),
            'ADDRESS.CITY' => $data->getADDRESSCITY(),
            'ADDRESS.COUNTRY' => $data->getADDRESSCOUNTRY(),

            //specific User Data
            'CRITERION.PAYOLUTION_CUSTOMER_GROUP' => $data->getCRITERIONPAYOLUTIONCUSTOMERGROUP(),
            'CRITERION.PAYOLUTION_CUSTOMER_LANGUAGE' => $data->getCRITERIONPAYOLUTIONCUSTOMERLANGUAGE(),

            'CRITERION.PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL' => $data->getCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONLEVEL(),
            'CRITERION.PAYOLUTION_CUSTOMER_REGISTRATION_DATE' => $data->getCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONDATE(),

            // shippingaddress
            'CRITERION.PAYOLUTION_SHIPPING_GIVEN' => $data->getCRITERIONPAYOLUTIONSHIPPINGGIVEN(),
            'CRITERION.PAYOLUTION_SHIPPING_FAMILY' => $data->getCRITERIONPAYOLUTIONSHIPPINGFAMILY(),
            'CRITERION.PAYOLUTION_SHIPPING_COUNTRY' =>  $data->getCRITERIONPAYOLUTIONSHIPPINGCOUNTRY(),
            'CRITERION.PAYOLUTION_SHIPPING_STREET' => $data->getCRITERIONPAYOLUTIONSHIPPINGSTREET(),
            'CRITERION.PAYOLUTION_SHIPPING_ZIP' => $data->getCRITERIONPAYOLUTIONSHIPPINGZIP(),
            'CRITERION.PAYOLUTION_SHIPPING_CITY' => $data->getCRITERIONPAYOLUTIONSHIPPINGCITY(),

            //system Data
            'CRITERION.PAYOLUTION_REQUEST_SYSTEM_VENDOR' => $data->getCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR(),
            'CRITERION.PAYOLUTION_REQUEST_SYSTEM_VERSION' => $data->getCRITERIONPAYOLUTIONMODULEVERSION(),
            'CRITERION.PAYOLUTION_REQUEST_SYSTEM_TYPE' => $data->getCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE(),
            'CRITERION.PAYOLUTION_WEBSHOP_URL' => $data->getCRITERIONPAYOLUTIONWEBSHOPURL(),
            'CRITERION.PAYOLUTION_MODULE_NAME' =>  $data->getCRITERIONPAYOLUTIONMODULENAME(),
            'CRITERION.PAYOLUTION_MODULE_VERSION' => $data->getCRITERIONPAYOLUTIONMODULEVERSION(),

            //basket Data
            'CRITERION.PAYOLUTION_TAX_AMOUNT' => $data->getCRITERIONPAYOLUTIONTAXAMOUNT(),
        );

        if ($payolutionConfig->isTestmode()) {
            $post_data['TRANSACTION.MODE'] = 'CONNECTOR_TEST';
        }


        if ($data->getIDENTIFICATIONREFERENCEID()) {
            $post_data['IDENTIFICATION.REFERENCEID'] = $data->getIDENTIFICATIONREFERENCEID();
        }

        $counter = 1;
        foreach ($data->getCRITERIONPAYOLUTIONITEMARRAY() as $position) {

            $post_data['CRITERION.PAYOLUTION_ITEM_DESCR_'.$counter] = $position['DESCR'];
            $post_data['CRITERION.PAYOLUTION_ITEM_PRICE_'.$counter] = $position['PRICE'];
            $post_data['CRITERION.PAYOLUTION_ITEM_TAX_'.$counter] = $position['TAX'];
            $counter += 1;
        }

        if ($data->getPAYOLUTIONPAYMENTMODE() == 'PAYOLUTION_INVOICE_B2B') {
            $post_data['ACCOUNT.BRAND'] = $data->getACCOUNTBRAND();
            $post_data['CRITERION.PAYOLUTION_TRX_TYPE'] = 'B2B';
            $post_data['TRANSACTION.CHANNEL'] = $payolutionConfig->getChannelB2bInvoice();
            $post_data['CRITERION.PAYOLUTION_SHIPPING_COMPANY'] = $data->getCRITERIONPAYOLUTIONSHIPPINGCOMPANY();
            $post_data['CRITERION.PAYOLUTION_COMPANY_NAME'] = $data->getCRITERIONPAYOLUTIONCOMPANYNAME();
            $post_data['CRITERION.PAYOLUTION_COMPANY_UID'] = $data->getCRITERIONPAYOLUTIONCOMPANYUID();
            unset($post_data['NAME.BIRTHDATE']);
        }

        if ($data->getCRITERIONPAYOLUTIONPRECHECKID() !== 'false') {
            $post_data['CRITERION.PAYOLUTION_PRE_CHECK_ID'] = $data->getCRITERIONPAYOLUTIONPRECHECKID();
        }


        return $post_data;
    }
}
