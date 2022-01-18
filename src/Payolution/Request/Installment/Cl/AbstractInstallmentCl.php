<?php
namespace Payolution\Request\Installment\Cl;

abstract class AbstractInstallmentCl
{
    private $REQUEST_VERSION;
    private $TRANSACTION_RESPONSE;
    private $IDENTIFICATION_TRANSACTIONID;
    private $PAYMENT_OPERATIONTYPE;
    private $PAYMENT_PAYMENTTYPE;
    private $PRESENTATION_AMOUNT;
    private $PRESENTATION_CURRENCY;
    private $PRESENTATION_USAGE;
    private $PRESENTATION_VAT;
    private $CRITERION_PAYOLUTION_CALCULATION_TARGET_COUNTRY;

    abstract public function getTRANSACTIONRESPONSE();

    abstract public function getREQUESTVERSION();

    abstract public function getIDENTIFICATIONTRANSACTIONID();

    abstract public function getPRESENTATIONAMOUNT();

    abstract public function getPRESENTATIONCURRENCY();

    abstract public function getPRESENTATIONVAT();

    abstract public function getPAYMENTOPERATIONTYPE();

    abstract public function getPAYMENTPAYMENTTYPE();

    abstract public function getPRESENTATIONUSAGE();

    abstract public function getCRITERIONPAYOLUTIONCALCULATIONTARGETCOUNTRY();
}
