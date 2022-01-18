<?php
namespace Payolution\Request\Installment\ExecutePayment;

use Payolution\Request\ExecutePayment\AbstractExecutePayment;

abstract class AbstractInstallmentExecutePayment extends AbstractExecutePayment
{
    private $CRITERION_PAYOLUTION_CALCULATION_ID;
    private $CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT;
    private $CRITERION_PAYOLUTION_DURATION;
    private $CRITERION_PAYOLUTION_ACCOUNT_HOLDER;
    private $CRITERION_PAYOLUTION_ACCOUNT_COUNTRY;
    private $CRITERION_PAYOLUTION_ACCOUNT_BIC;
    private $CRITERION_PAYOLUTION_ACCOUNT_IBAN;

    abstract public function getCRITERIONPAYOLUTIONCALCULATIONID();

    abstract public function getCRITERIONPAYOLUTIONINSTALLMENTAMOUNT();

    abstract public function getCRITERIONPAYOLUTIONDURATION();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTHOLDER();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTCOUNTRY();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTBIC();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTIBAN();
}