<?php
namespace PolPaymentPayolution\Installment\Cl;

class CreateRequestArray
{

    /**
     * constructor class
     *
     */
    public function __construct()
    {
    }

    /**
     * create Request Array from Shopware Variables
     *
     * @param $amount
     * @param $amountNet
     * @param $country
     * @return array
     */
    public function createArray($amount, $amountNet, $country)
    {
        $return = array(
            'setREQUESTVERSION' => '2.0',
            'setTRANSACTIONRESPONSE' => 'SYNC',
            'setIDENTIFICATIONTRANSACTIONID' => '',
            'setPAYMENTOPERATIONTYPE' => 'CALCULATION',
            'setPAYMENTPAYMENTTYPE' => 'INSTALLMENT',
            'setPRESENTATIONAMOUNT' => $amount,
            'setPRESENTATIONCURRENCY' => Shopware()->Shop()->getCurrency()->getCurrency(),
            'setPRESENTATIONUSAGE' => 'Order with ID ',
            'setPRESENTATIONVAT' => (string) round($amount - $amountNet, 2),
            'setCRITERIONPAYOLUTIONCALCULATIONTARGETCOUNTRY' => $country,
        );

        return $return;
    }

}