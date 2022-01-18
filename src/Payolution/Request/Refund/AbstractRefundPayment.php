<?php
namespace Payolution\Request\Refund;

abstract class AbstractRefundPayment
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
    private $PAYOLUTION_PAYMENT_MODE;

    abstract public function getPAYOLUTIONPAYMENTMODE();

    abstract public function getTRANSACTIONRESPONSE();

    abstract public function getPAYMENTCODE();

    abstract public function getIDENTIFICATIONREFERENCEID();

    abstract public function getPRESENTATIONUSAGE();

    abstract public function getCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR();

    abstract public function getCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION();

    abstract public function getCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE();

    abstract public function getREQUESTVERSION();

    abstract public function getIDENTIFICATIONTRANSACTIONID();

    abstract public function getIDENTIFICATIONINVOICEID();

    abstract public function getPRESENTATIONAMOUNT();

    abstract public function getPRESENTATIONCURRENCY();

    abstract public function getCRITERIONPAYOLUTIONTAXAMOUNT();

    abstract public function getCRITERIONPAYOLUTIONMODULENAME();

    abstract public function getCRITERIONPAYOLUTIONMODULEVERSION();
}
