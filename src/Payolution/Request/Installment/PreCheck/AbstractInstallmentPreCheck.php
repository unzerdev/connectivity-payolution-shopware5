<?php
namespace Payolution\Request\Installment\PreCheck;

use Payolution\Request\PreCheck\AbstractPreCheckPayment;

abstract class AbstractInstallmentPreCheck extends AbstractPreCheckPayment
{
    private $CRITERION_PAYOLUTION_CALCULATION_ID;
    private $CRITERION_PAYOLUTION_ACCOUNT_HOLDER;
    private $CRITERION_PAYOLUTION_ACCOUNT_COUNTRY;
    private $CRITERION_PAYOLUTION_ACCOUNT_BIC;
    private $CRITERION_PAYOLUTION_ACCOUNT_IBAN;
    private $CRITERION_PAYOLUTION_DURATION;
    private $CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT;

    abstract public function getCRITERIONPAYOLUTIONCALCULATIONID();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTHOLDER();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTCOUNTRY();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTBIC();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTIBAN();

    abstract public function getCRITERIONPAYOLUTIONDURATION();

    abstract public function getCRITERIONPAYOLUTIONINSTALLMENTAMOUNT();
}