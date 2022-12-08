<?php
namespace PolPaymentPayolution\Request;

use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Shopware\Models\Shop\Shop;

class CreateRequestArray
{

    /** @var Shop */
    private $shop;

    /** @var Enlight_Components_Db_Adapter_Pdo_Mysql */
    private $db;

    /** @var string */
    private $pluginName;

    /** @var string */
    private $pluginVersion;

    public function __construct(
        Shop $shop,
        Enlight_Components_Db_Adapter_Pdo_Mysql $db,
        string $pluginName,
        string $pluginVersion
    ) {
        $this->shop          = $shop;
        $this->db            = $db;
        $this->pluginName    = $pluginName;
        $this->pluginVersion = $pluginVersion;
    }

    /**
     * create Request Array from Shopware Variables
     *
     * @param $basket
     * @param $user
     * @return array
     */
    public function createArray($basket, $user, $preCheck, $mode = 'PAYOLUTION_INVOICE', $taxFree = 0)
    {
        if ($taxFree == 1) {
            foreach ($basket['content'] as $position) {
                $items[] = array(
                    'DESCR' => $position['articlename'],
                    'PRICE' => round((float) str_replace(',', '.', $position['amountnet']), 2),
                    'TAX' => '0.00',
                );
            }
        } else {
            foreach ($basket['content'] as $position) {
                if (!isset($position['amountWithTax']) || empty($position['amountWithTax'])) {
                    $position['amountWithTax'] = $position['amount'];
                }

                $items[] = array(
                    'DESCR' => $position['articlename'],
                    'PRICE' => round((float) str_replace(',', '.', $position['amountWithTax']), 2),
                    'TAX' => str_replace(',', '.', $position['tax']),
                );
            }

            if (!empty($basket['AmountWithTaxNumeric']) && isset($basket['AmountWithTaxNumeric'])) {
                $basket['AmountNumeric'] = $basket['AmountWithTaxNumeric'];
            }
        }

        $return = array(
            'setREQUESTVERSION' => '1.0',
            'setTRANSACTIONRESPONSE' => 'SYNC',
            'setPAYMENTCODE' => 'VA.PA',
            'setACCOUNTBRAND' => 'PAYOLUTION_INVOICE',
            'setIDENTIFICATIONTRANSACTIONID' => '',
            'setIDENTIFICATIONSHOPPERID' => $user['additional']['user']['customernumber'],
            'setPRESENTATIONAMOUNT' => $basket['AmountNumeric'],
            'setPRESENTATIONCURRENCY' => $this->shop->getCurrency()->getCurrency(),
            'setPRESENTATIONUSAGE' => 'Trx ',
            'setNAMESEX' => $user['billingaddress']['salutation'] == 'mr' ? 'M' : 'F',
            'setNAMEGIVEN' => $user['billingaddress']['firstname'],
            'setNAMEFAMILY' => $user['billingaddress']['lastname'],
            'setNAMEBIRTHDATE' => $user['additional']['user']['birthday'],
            'setCONTACTEMAIL' => $user['additional']['user']['email'],
            'setCONTACTPHONE' => $user['billingaddress']['phone'],
            'setCONTACTIP' => $_SERVER['REMOTE_ADDR'], /**@TODO use Shopware Method ?*/
            'setADDRESSSTREET' => $user['billingaddress']['street'],
            'setADDRESSZIP' => $user['billingaddress']['zipcode'],
            'setADDRESSCITY' => $user['billingaddress']['city'],
            'setADDRESSCOUNTRY' => $user['additional']['country']['countryiso'],
            'setCRITERIONPAYOLUTIONCUSTOMERGROUP' => $user['additional']['user']['customergroup'],
            'setCRITERIONPAYOLUTIONCUSTOMERLANGUAGE' => substr($this->shop->getLocale()->getLocale(), 0, 2),
            'setCRITERIONPAYOLUTIONSHIPPINGCOMPANY' => $user['shippingaddress']['company'],
            'setCRITERIONPAYOLUTIONSHIPPINGGIVEN' => $user['shippingaddress']['firstname'],
            'setCRITERIONPAYOLUTIONSHIPPINGFAMILY' => $user['shippingaddress']['lastname'],
            'setCRITERIONPAYOLUTIONSHIPPINGCOUNTRY' => $user['additional']['country']['countryiso'],
            'setCRITERIONPAYOLUTIONSHIPPINGSTREET' => $user['shippingaddress']['street'],
            'setCRITERIONPAYOLUTIONSHIPPINGZIP' => $user['shippingaddress']['zipcode'],
            'setCRITERIONPAYOLUTIONSHIPPINGCITY' => $user['shippingaddress']['city'],
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR' => 'Shopware_PHP_POST',
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION' => 'Shopware',
            'setCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE' => 'Webshop',
            'setCRITERIONPAYOLUTIONWEBSHOPURL' => $this->shop->getHost(),
            //Todo: add parameter from dic PAYOL-258
            'setCRITERIONPAYOLUTIONMODULENAME' => $this->pluginName,
            'setCRITERIONPAYOLUTIONMODULEVERSION' => $this->pluginVersion,
            'setCRITERIONPAYOLUTIONTAXAMOUNT' => ($basket['AmountNumeric']-$basket['AmountNetNumeric']),
            'setCRITERIONPAYOLUTIONCOMPANYNAME' => $user['billingaddress']['company'],
            'setCRITERIONPAYOLUTIONCOMPANYUID' => $user['billingaddress']['ustid'],
            'setCRITERIONPAYOLUTIONPRECHECKID' => false,
            'setCRITERIONPAYOLUTIONITEMARRAY' => $items,
            'setCRITERIONPAYOLUTIONPRECHECK' => 'true',
            'setPAYOLUTIONPAYMENTMODE' => $mode,
            'setCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONDATE' => str_replace('-', '', $user['additional']['user']['firstlogin']),
            'setCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONLEVEL' => $user['additional']['user']['accountmode'] == 0 ? 1 : 0,
        );

        if ($mode === 'PAYOLUTION_INS') {
            $installmentData = $this->db->fetchRow(
                'SELECT
                  *
                FROM
                  bestit_payolution_installment
                WHERE
                  userId = :userId',
                array(
                    ':userId' => $user['additional']['user']['id']
                )
            );
            $return['setACCOUNTBRAND'] = 'PAYOLUTION_INS';
            $return['setCRITERIONPAYOLUTIONCALCULATIONID'] = $installmentData['clId'];
            $return['setCRITERIONPAYOLUTIONINSTALLMENTAMOUNT'] = $installmentData['amount'];
            $return['setCRITERIONPAYOLUTIONDURATION'] = $installmentData['duration'];
            $return['setCRITERIONPAYOLUTIONACCOUNTHOLDER'] = $installmentData['accountHolder'];
            $return['setCRITERIONPAYOLUTIONACCOUNTCOUNTRY'] = $user['additional']['country']['countryiso'];
            $return['setCRITERIONPAYOLUTIONACCOUNTBIC'] = $installmentData['accountBic'];
            $return['setCRITERIONPAYOLUTIONACCOUNTIBAN'] = $installmentData['accountIban'];
            $return['setCRITERIONPAYOLUTIONPRECHECKID'] = $installmentData['pcId'];
        }

        if ($mode === 'PAYOLUTION_ELV') {
            $return['setACCOUNTBRAND'] = 'PAYOLUTION_ELV';
            $return['setCRITERIONPAYOLUTIONACCOUNTHOLDER'] = $user['payolution_elv']['accountHolder'];
            $return['setCRITERIONPAYOLUTIONACCOUNTCOUNTRY'] = $user['additional']['country']['countryiso'];
            $return['setCRITERIONPAYOLUTIONACCOUNTBIC'] = $user['payolution_elv']['accountBic'];
            $return['setCRITERIONPAYOLUTIONACCOUNTIBAN'] = $user['payolution_elv']['accountIban'];
            $return['setCRITERIONPAYOLUTIONCUSTOMERNUMBER'] = $user['additional']['user']['customernumber'];
        }

        if ($preCheck === false) {
            unset($return['setCRITERIONPAYOLUTIONPRECHECK']);
        }
        return $return;
    }

}
