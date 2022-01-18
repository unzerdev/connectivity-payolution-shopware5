<?php
namespace Payolution\Request\ELV\PreCheck;

use Payolution\Request\PreCheck\AbstractPreCheckPayment;

abstract class AbstractELVPreCheck extends AbstractPreCheckPayment
{
    private $CRITERION_PAYOLUTION_ACCOUNT_HOLDER;
    private $CRITERION_PAYOLUTION_ACCOUNT_COUNTRY;
    private $CRITERION_PAYOLUTION_ACCOUNT_BIC;
    private $CRITERION_PAYOLUTION_ACCOUNT_IBAN;
    private $CRITERION_PAYOLUTION_CUSTOMER_NUMBER;

    abstract public function getCRITERIONPAYOLUTIONACCOUNTHOLDER();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTCOUNTRY();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTBIC();

    abstract public function getCRITERIONPAYOLUTIONACCOUNTIBAN();

    abstract public function getCRITERIONPAYOLUTIONCUSTOMERNUMBER();
}
