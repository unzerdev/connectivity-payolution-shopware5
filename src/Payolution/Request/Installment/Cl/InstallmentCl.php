<?php
namespace Payolution\Request\Installment\Cl;

class InstallmentCl extends AbstractInstallmentCl
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
    public function getPAYMENTOPERATIONTYPE()
    {
        return $this->PAYMENT_OPERATIONTYPE;
    }

    /**
     * @param mixed $PAYMENT_OPERATIONTYPE
     */
    public function setPAYMENTOPERATIONTYPE($PAYMENT_OPERATIONTYPE)
    {
        $this->PAYMENT_OPERATIONTYPE = $PAYMENT_OPERATIONTYPE;
    }

    /**
     * @return mixed
     */
    public function getPAYMENTPAYMENTTYPE()
    {
        return $this->PAYMENT_PAYMENTTYPE;
    }

    /**
     * @param mixed $PAYMENT_PAYMENTTYPE
     */
    public function setPAYMENTPAYMENTTYPE($PAYMENT_PAYMENTTYPE)
    {
        $this->PAYMENT_PAYMENTTYPE = $PAYMENT_PAYMENTTYPE;
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
    public function getPRESENTATIONVAT()
    {
        return $this->PRESENTATION_VAT;
    }

    /**
     * @param mixed $PRESENTATION_VAT
     */
    public function setPRESENTATIONVAT($PRESENTATION_VAT)
    {
        $this->PRESENTATION_VAT = $PRESENTATION_VAT;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCALCULATIONTARGETCOUNTRY()
    {
        return $this->CRITERION_PAYOLUTION_CALCULATION_TARGET_COUNTRY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CALCULATION_TARGET_COUNTRY
     */
    public function setCRITERIONPAYOLUTIONCALCULATIONTARGETCOUNTRY($CRITERION_PAYOLUTION_CALCULATION_TARGET_COUNTRY)
    {
        $this->CRITERION_PAYOLUTION_CALCULATION_TARGET_COUNTRY = $CRITERION_PAYOLUTION_CALCULATION_TARGET_COUNTRY;
    }
}
