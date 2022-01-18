<?php
namespace Payolution\Request\PreCheck;

use Payolution\Request\ExecutePayment\AbstractExecutePayment;

abstract class AbstractPreCheckPayment extends AbstractExecutePayment
{
    private $CRITERION_PAYOLUTION_PRE_CHECK;

    abstract public function getCRITERIONPAYOLUTIONPRECHECK();
}
