<?php
namespace Payolution\Request\Capture;

class CapturePayment extends AbstractCapturePayment
{
    private $REQUEST_VERSION;
    private $TRANSACTION_RESPONSE;
    private $PAYMENT_CODE;
    private $IDENTIFICATION_REFERENCEID;
    private $PRESENTATION_USAGE;
    private $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR;
    private $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION;
    private $CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE;
    private $IDENTIFICATION_TRANSACTIONID;
    private $IDENTIFICATION_INVOICEID;
    private $PRESENTATION_AMOUNT;
    private $PRESENTATION_CURRENCY;
    private $CRITERION_PAYOLUTION_TAX_AMOUNT;
    private $CRITERION_PAYOLUTION_MODULE_NAME;
    private $CRITERION_PAYOLUTION_MODULE_VERSION;
    private $CRITERION_PAYOLUTION_TRANSPORTATION_TRACKING;
    private $CRITERION_PAYOLUTION_TRANSPORTATION_RETURN_TRACKING;
    private $CRITERION_PAYOLUTION_TRANSPORTATION_COMPANY;
    private $CRITERION_PAYOLUTION_ORDER_ID;
    private $CRITERION_PAYOLUTION_INVOICE_ID;
    private $CRITERION_PAYOLUTION_CUSTOMER_NUMBER;
    private $IDENTIFICATION_SHOPPERID;
    private $PAYOLUTION_PAYMENT_MODE;

    /**
     * @return mixed
     */
    public function getPAYOLUTIONPAYMENTMODE()
    {
        return $this->PAYOLUTION_PAYMENT_MODE;
    }

    /**
     * @param mixed $PAYOLUTION_PAYMENT_MODE
     */
    public function setPAYOLUTIONPAYMENTMODE($PAYOLUTION_PAYMENT_MODE)
    {
        $this->PAYOLUTION_PAYMENT_MODE = $PAYOLUTION_PAYMENT_MODE;
    }

    /**
     * @return mixed
     */
    public function getIDENTIFICATIONSHOPPERID()
    {
        return $this->IDENTIFICATION_SHOPPERID;
    }

    /**
     * @param mixed $IDENTIFICATION_SHOPPERID
     */
    public function setIDENTIFICATIONSHOPPERID($IDENTIFICATION_SHOPPERID)
    {
        $this->IDENTIFICATION_SHOPPERID = $IDENTIFICATION_SHOPPERID;
    }
    /**
     * @return mixed
     */
    public function getSECURITYSENDER()
    {
        return $this->SECURITY_SENDER;
    }

    /**
     * @param mixed $SECURITY_SENDER
     */
    public function setSECURITYSENDER($SECURITY_SENDER)
    {
        $this->SECURITY_SENDER = $SECURITY_SENDER;
    }

    /**
     * @return mixed
     */
    public function getUSERLOGIN()
    {
        return $this->USER_LOGIN;
    }

    /**
     * @param mixed $USER_LOGIN
     */
    public function setUSERLOGIN($USER_LOGIN)
    {
        $this->USER_LOGIN = $USER_LOGIN;
    }

    /**
     * @return mixed
     */
    public function getUSERPWD()
    {
        return $this->USER_PWD;
    }

    /**
     * @param mixed $USER_PWD
     */
    public function setUSERPWD($USER_PWD)
    {
        $this->USER_PWD = $USER_PWD;
    }

    /**
     * @return mixed
     */
    public function getREQUESTVERSION()
    {
        return $this->REQUEST_VERSION;
    }

    /**
     * @param mixed $REQUEST_VERSION
     */
    public function setREQUESTVERSION($REQUEST_VERSION)
    {
        $this->REQUEST_VERSION = $REQUEST_VERSION;
    }

    /**
     * @return mixed
     */
    public function getTRANSACTIONRESPONSE()
    {
        return $this->TRANSACTION_RESPONSE;
    }

    /**
     * @param mixed $TRANSACTION_RESPONSE
     */
    public function setTRANSACTIONRESPONSE($TRANSACTION_RESPONSE)
    {
        $this->TRANSACTION_RESPONSE = $TRANSACTION_RESPONSE;
    }

    /**
     * @return mixed
     */
    public function getPAYMENTCODE()
    {
        return $this->PAYMENT_CODE;
    }

    /**
     * @param mixed $PAYMENT_CODE
     */
    public function setPAYMENTCODE($PAYMENT_CODE)
    {
        $this->PAYMENT_CODE = $PAYMENT_CODE;
    }

    /**
     * @return mixed
     */
    public function getIDENTIFICATIONREFERENCEID()
    {
        return $this->IDENTIFICATION_REFERENCEID;
    }

    /**
     * @param mixed $IDENTIFICATION_REFERENCEID
     */
    public function setIDENTIFICATIONREFERENCEID($IDENTIFICATION_REFERENCEID)
    {
        $this->IDENTIFICATION_REFERENCEID = $IDENTIFICATION_REFERENCEID;
    }

    /**
     * @return mixed
     */
    public function getPRESENTATIONUSAGE()
    {
        return $this->PRESENTATION_USAGE;
    }

    /**
     * @param mixed $PRESENTATION_USAGE
     */
    public function setPRESENTATIONUSAGE($PRESENTATION_USAGE)
    {
        $this->PRESENTATION_USAGE = $PRESENTATION_USAGE;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR()
    {
        return $this->CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR
     */
    public function setCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR($CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR)
    {
        $this->CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR = $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION()
    {
        return $this->CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION
     */
    public function setCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION($CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION)
    {
        $this->CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION = $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE()
    {
        return $this->CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE
     */
    public function setCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE($CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE)
    {
        $this->CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE = $CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE;
    }

    /**
     * @return mixed
     */
    public function getIDENTIFICATIONTRANSACTIONID()
    {
        return $this->IDENTIFICATION_TRANSACTIONID;
    }

    /**
     * @param mixed $IDENTIFICATION_TRANSACTIONID
     */
    public function setIDENTIFICATIONTRANSACTIONID($IDENTIFICATION_TRANSACTIONID)
    {
        $this->IDENTIFICATION_TRANSACTIONID = $IDENTIFICATION_TRANSACTIONID;
    }

    /**
     * @return mixed
     */
    public function getIDENTIFICATIONINVOICEID()
    {
        return $this->IDENTIFICATION_INVOICEID;
    }

    /**
     * @param mixed $IDENTIFICATION_INVOICEID
     */
    public function setIDENTIFICATIONINVOICEID($IDENTIFICATION_INVOICEID)
    {
        $this->IDENTIFICATION_INVOICEID = $IDENTIFICATION_INVOICEID;
    }

    /**
     * @return mixed
     */
    public function getPRESENTATIONAMOUNT()
    {
        return $this->PRESENTATION_AMOUNT;
    }

    /**
     * @param mixed $PRESENTATION_AMOUNT
     */
    public function setPRESENTATIONAMOUNT($PRESENTATION_AMOUNT)
    {
        $this->PRESENTATION_AMOUNT = $PRESENTATION_AMOUNT;
    }

    /**
     * @return mixed
     */
    public function getPRESENTATIONCURRENCY()
    {
        return $this->PRESENTATION_CURRENCY;
    }

    /**
     * @param mixed $PRESENTATION_CURRENCY
     */
    public function setPRESENTATIONCURRENCY($PRESENTATION_CURRENCY)
    {
        $this->PRESENTATION_CURRENCY = $PRESENTATION_CURRENCY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONTAXAMOUNT()
    {
        return $this->CRITERION_PAYOLUTION_TAX_AMOUNT;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_TAX_AMOUNT
     */
    public function setCRITERIONPAYOLUTIONTAXAMOUNT($CRITERION_PAYOLUTION_TAX_AMOUNT)
    {
        $this->CRITERION_PAYOLUTION_TAX_AMOUNT = $CRITERION_PAYOLUTION_TAX_AMOUNT;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONMODULENAME()
    {
        return $this->CRITERION_PAYOLUTION_MODULE_NAME;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_MODULE_NAME
     */
    public function setCRITERIONPAYOLUTIONMODULENAME($CRITERION_PAYOLUTION_MODULE_NAME)
    {
        $this->CRITERION_PAYOLUTION_MODULE_NAME = $CRITERION_PAYOLUTION_MODULE_NAME;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONMODULEVERSION()
    {
        return $this->CRITERION_PAYOLUTION_MODULE_VERSION;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_MODULE_VERSION
     */
    public function setCRITERIONPAYOLUTIONMODULEVERSION($CRITERION_PAYOLUTION_MODULE_VERSION)
    {
        $this->CRITERION_PAYOLUTION_MODULE_VERSION = $CRITERION_PAYOLUTION_MODULE_VERSION;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONTRANSPORTATIONTRACKING()
    {
        return $this->CRITERION_PAYOLUTION_TRANSPORTATION_TRACKING;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_TRANSPORTATION_TRACKING
     */
    public function setCRITERIONPAYOLUTIONTRANSPORTATIONTRACKING($CRITERION_PAYOLUTION_TRANSPORTATION_TRACKING)
    {
        $this->CRITERION_PAYOLUTION_TRANSPORTATION_TRACKING = $CRITERION_PAYOLUTION_TRANSPORTATION_TRACKING;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONTRANSPORTATIONRETURNTRACKING()
    {
        return $this->CRITERION_PAYOLUTION_TRANSPORTATION_RETURN_TRACKING;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_TRANSPORTATION_RETURN_TRACKING
     */
    public function setCRITERIONPAYOLUTIONTRANSPORTATIONRETURNTRACKING($CRITERION_PAYOLUTION_TRANSPORTATION_RETURN_TRACKING)
    {
        $this->CRITERION_PAYOLUTION_TRANSPORTATION_RETURN_TRACKING = $CRITERION_PAYOLUTION_TRANSPORTATION_RETURN_TRACKING;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONTRANSPORTATIONCOMPANY()
    {
        return $this->CRITERION_PAYOLUTION_TRANSPORTATION_COMPANY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_TRANSPORTATION_COMPANY
     */
    public function setCRITERIONPAYOLUTIONTRANSPORTATIONCOMPANY($CRITERION_PAYOLUTION_TRANSPORTATION_COMPANY)
    {
        $this->CRITERION_PAYOLUTION_TRANSPORTATION_COMPANY = $CRITERION_PAYOLUTION_TRANSPORTATION_COMPANY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONORDERID()
    {
        return $this->CRITERION_PAYOLUTION_ORDER_ID;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_ORDER_ID
     */
    public function setCRITERIONPAYOLUTIONORDERID($CRITERION_PAYOLUTION_ORDER_ID)
    {
        $this->CRITERION_PAYOLUTION_ORDER_ID = $CRITERION_PAYOLUTION_ORDER_ID;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONINVOICEID()
    {
        return $this->CRITERION_PAYOLUTION_INVOICE_ID;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_INVOICE_ID
     */
    public function setCRITERIONPAYOLUTIONINVOICEID($CRITERION_PAYOLUTION_INVOICE_ID)
    {
        $this->CRITERION_PAYOLUTION_INVOICE_ID = $CRITERION_PAYOLUTION_INVOICE_ID;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCUSTOMERNUMBER()
    {
        return $this->CRITERION_PAYOLUTION_CUSTOMER_NUMBER;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CUSTOMER_NUMBER
     */
    public function setCRITERIONPAYOLUTIONCUSTOMERNUMBER($CRITERION_PAYOLUTION_CUSTOMER_NUMBER)
    {
        $this->CRITERION_PAYOLUTION_CUSTOMER_NUMBER = $CRITERION_PAYOLUTION_CUSTOMER_NUMBER;
    }
}
