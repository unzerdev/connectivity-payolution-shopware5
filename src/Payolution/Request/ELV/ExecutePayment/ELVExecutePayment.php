<?php
namespace Payolution\Request\ELV\ExecutePayment;

class ELVExecutePayment extends AbstractELVExecutePayment
{
    private $REQUEST_VERSION;
    private $TRANSACTION_RESPONSE;
    private $PAYMENT_CODE;
    private $ACCOUNT_BRAND;
    private $IDENTIFICATION_TRANSACTIONID;
    private $IDENTIFICATION_REFERENCEID;
    private $IDENTIFICATION_SHOPPERID;
    private $PRESENTATION_AMOUNT;
    private $PRESENTATION_CURRENCY;
    private $PRESENTATION_USAGE;
    private $NAME_SEX;
    private $NAME_GIVEN;
    private $NAME_FAMILY;
    private $NAME_BIRTHDATE;
    private $CONTACT_EMAIL;
    private $CONTACT_PHONE;
    private $CONTACT_IP;
    private $ADDRESS_STREET;
    private $ADDRESS_ZIP;
    private $ADDRESS_CITY;
    private $ADDRESS_COUNTRY;
    private $CRITERION_PAYOLUTION_CUSTOMER_GROUP;
    private $CRITERION_PAYOLUTION_CUSTOMER_LANGUAGE;
    private $CRITERION_PAYOLUTION_SHIPPING_COMPANY;
    private $CRITERION_PAYOLUTION_SHIPPING_GIVEN;
    private $CRITERION_PAYOLUTION_SHIPPING_FAMILY;
    private $CRITERION_PAYOLUTION_SHIPPING_COUNTRY;
    private $CRITERION_PAYOLUTION_SHIPPING_STREET;
    private $CRITERION_PAYOLUTION_SHIPPING_ZIP;
    private $CRITERION_PAYOLUTION_SHIPPING_CITY;
    private $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VENDOR;
    private $CRITERION_PAYOLUTION_REQUEST_SYSTEM_VERSION;
    private $CRITERION_PAYOLUTION_REQUEST_SYSTEM_TYPE;
    private $CRITERION_PAYOLUTION_WEBSHOP_URL;
    private $CRITERION_PAYOLUTION_MODULE_NAME;
    private $CRITERION_PAYOLUTION_MODULE_VERSION;
    private $CRITERION_PAYOLUTION_TAX_AMOUNT;
    private $CRITERION_PAYOLUTION_COMPANY_NAME;
    private $CRITERION_PAYOLUTION_COMPANY_UID;
    private $CRITERION_PAYOLUTION_PRE_CHECK_ID;
    private $CRITERION_PAYOLUTION_ITEM_ARRAY;
    private $CRITERION_PAYOLUTION_PRE_CHECK;
    private $PAYOLUTION_PAYMENT_MODE;
    private $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE;
    private $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL;
    private $CRITERION_PAYOLUTION_ACCOUNT_HOLDER;
    private $CRITERION_PAYOLUTION_ACCOUNT_COUNTRY;
    private $CRITERION_PAYOLUTION_ACCOUNT_BIC;
    private $CRITERION_PAYOLUTION_ACCOUNT_IBAN;
    private $CRITERION_PAYOLUTION_CUSTOMER_NUMBER;

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

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONDATE()
    {
        return $this->CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE
     */
    public function setCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONDATE($CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE)
    {
        $this->CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE = $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONLEVEL()
    {
        return $this->CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL
     */
    public function setCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONLEVEL($CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL)
    {
        $this->CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL = $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL;
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
    public function getCRITERIONPAYOLUTIONPRECHECK()
    {
        return $this->CRITERION_PAYOLUTION_PRE_CHECK;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_PRE_CHECK
     */
    public function setCRITERIONPAYOLUTIONPRECHECK($CRITERION_PAYOLUTION_PRE_CHECK)
    {
        $this->CRITERION_PAYOLUTION_PRE_CHECK = $CRITERION_PAYOLUTION_PRE_CHECK;
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
    public function getACCOUNTBRAND()
    {
        return $this->ACCOUNT_BRAND;
    }

    /**
     * @param mixed $ACCOUNT_BRAND
     */
    public function setACCOUNTBRAND($ACCOUNT_BRAND)
    {
        $this->ACCOUNT_BRAND = $ACCOUNT_BRAND;
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
    public function getNAMESEX()
    {
        return $this->NAME_SEX;
    }

    /**
     * @param mixed $NAME_SEX
     */
    public function setNAMESEX($NAME_SEX)
    {
        $this->NAME_SEX = $NAME_SEX;
    }

    /**
     * @return mixed
     */
    public function getNAMEGIVEN()
    {
        return $this->NAME_GIVEN;
    }

    /**
     * @param mixed $NAME_GIVEN
     */
    public function setNAMEGIVEN($NAME_GIVEN)
    {
        $this->NAME_GIVEN = $NAME_GIVEN;
    }

    /**
     * @return mixed
     */
    public function getNAMEFAMILY()
    {
        return $this->NAME_FAMILY;
    }

    /**
     * @param mixed $NAME_FAMILY
     */
    public function setNAMEFAMILY($NAME_FAMILY)
    {
        $this->NAME_FAMILY = $NAME_FAMILY;
    }

    /**
     * @return mixed
     */
    public function getNAMEBIRTHDATE()
    {
        return $this->NAME_BIRTHDATE;
    }

    /**
     * @param mixed $NAME_BIRTHDATE
     */
    public function setNAMEBIRTHDATE($NAME_BIRTHDATE)
    {
        $this->NAME_BIRTHDATE = $NAME_BIRTHDATE;
    }

    /**
     * @return mixed
     */
    public function getCONTACTEMAIL()
    {
        return $this->CONTACT_EMAIL;
    }

    /**
     * @param mixed $CONTACT_EMAIL
     */
    public function setCONTACTEMAIL($CONTACT_EMAIL)
    {
        $this->CONTACT_EMAIL = $CONTACT_EMAIL;
    }

    /**
     * @return mixed
     */
    public function getCONTACTPHONE()
    {
        return $this->CONTACT_PHONE;
    }

    /**
     * @param mixed $CONTACT_PHONE
     */
    public function setCONTACTPHONE($CONTACT_PHONE)
    {
        $this->CONTACT_PHONE = $CONTACT_PHONE;
    }

    /**
     * @return mixed
     */
    public function getCONTACTIP()
    {
        return $this->CONTACT_IP;
    }

    /**
     * @param mixed $CONTACT_IP
     */
    public function setCONTACTIP($CONTACT_IP)
    {
        $this->CONTACT_IP = $CONTACT_IP;
    }

    /**
     * @return mixed
     */
    public function getADDRESSSTREET()
    {
        return $this->ADDRESS_STREET;
    }

    /**
     * @param mixed $ADDRESS_STREET
     */
    public function setADDRESSSTREET($ADDRESS_STREET)
    {
        $this->ADDRESS_STREET = $ADDRESS_STREET;
    }

    /**
     * @return mixed
     */
    public function getADDRESSZIP()
    {
        return $this->ADDRESS_ZIP;
    }

    /**
     * @param mixed $ADDRESS_ZIP
     */
    public function setADDRESSZIP($ADDRESS_ZIP)
    {
        $this->ADDRESS_ZIP = $ADDRESS_ZIP;
    }

    /**
     * @return mixed
     */
    public function getADDRESSCITY()
    {
        return $this->ADDRESS_CITY;
    }

    /**
     * @param mixed $ADDRESS_CITY
     */
    public function setADDRESSCITY($ADDRESS_CITY)
    {
        $this->ADDRESS_CITY = $ADDRESS_CITY;
    }

    /**
     * @return mixed
     */
    public function getADDRESSCOUNTRY()
    {
        return $this->ADDRESS_COUNTRY;
    }

    /**
     * @param mixed $ADDRESS_COUNTRY
     */
    public function setADDRESSCOUNTRY($ADDRESS_COUNTRY)
    {
        $this->ADDRESS_COUNTRY = $ADDRESS_COUNTRY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCUSTOMERGROUP()
    {
        return $this->CRITERION_PAYOLUTION_CUSTOMER_GROUP;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CUSTOMER_GROUP
     */
    public function setCRITERIONPAYOLUTIONCUSTOMERGROUP($CRITERION_PAYOLUTION_CUSTOMER_GROUP)
    {
        $this->CRITERION_PAYOLUTION_CUSTOMER_GROUP = $CRITERION_PAYOLUTION_CUSTOMER_GROUP;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCUSTOMERLANGUAGE()
    {
        return $this->CRITERION_PAYOLUTION_CUSTOMER_LANGUAGE;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CUSTOMER_LANGUAGE
     */
    public function setCRITERIONPAYOLUTIONCUSTOMERLANGUAGE($CRITERION_PAYOLUTION_CUSTOMER_LANGUAGE)
    {
        $this->CRITERION_PAYOLUTION_CUSTOMER_LANGUAGE = $CRITERION_PAYOLUTION_CUSTOMER_LANGUAGE;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGCOMPANY()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_COMPANY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_COMPANY
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGCOMPANY($CRITERION_PAYOLUTION_SHIPPING_COMPANY)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_COMPANY = $CRITERION_PAYOLUTION_SHIPPING_COMPANY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGGIVEN()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_GIVEN;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_GIVEN
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGGIVEN($CRITERION_PAYOLUTION_SHIPPING_GIVEN)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_GIVEN = $CRITERION_PAYOLUTION_SHIPPING_GIVEN;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGFAMILY()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_FAMILY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_FAMILY
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGFAMILY($CRITERION_PAYOLUTION_SHIPPING_FAMILY)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_FAMILY = $CRITERION_PAYOLUTION_SHIPPING_FAMILY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGCOUNTRY()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_COUNTRY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_COUNTRY
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGCOUNTRY($CRITERION_PAYOLUTION_SHIPPING_COUNTRY)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_COUNTRY = $CRITERION_PAYOLUTION_SHIPPING_COUNTRY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGSTREET()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_STREET;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_STREET
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGSTREET($CRITERION_PAYOLUTION_SHIPPING_STREET)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_STREET = $CRITERION_PAYOLUTION_SHIPPING_STREET;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGZIP()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_ZIP;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_ZIP
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGZIP($CRITERION_PAYOLUTION_SHIPPING_ZIP)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_ZIP = $CRITERION_PAYOLUTION_SHIPPING_ZIP;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONSHIPPINGCITY()
    {
        return $this->CRITERION_PAYOLUTION_SHIPPING_CITY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_SHIPPING_CITY
     */
    public function setCRITERIONPAYOLUTIONSHIPPINGCITY($CRITERION_PAYOLUTION_SHIPPING_CITY)
    {
        $this->CRITERION_PAYOLUTION_SHIPPING_CITY = $CRITERION_PAYOLUTION_SHIPPING_CITY;
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
    public function getCRITERIONPAYOLUTIONWEBSHOPURL()
    {
        return $this->CRITERION_PAYOLUTION_WEBSHOP_URL;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_WEBSHOP_URL
     */
    public function setCRITERIONPAYOLUTIONWEBSHOPURL($CRITERION_PAYOLUTION_WEBSHOP_URL)
    {
        $this->CRITERION_PAYOLUTION_WEBSHOP_URL = $CRITERION_PAYOLUTION_WEBSHOP_URL;
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
    public function getCRITERIONPAYOLUTIONCOMPANYNAME()
    {
        return $this->CRITERION_PAYOLUTION_COMPANY_NAME;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_COMPANY_NAME
     */
    public function setCRITERIONPAYOLUTIONCOMPANYNAME($CRITERION_PAYOLUTION_COMPANY_NAME)
    {
        $this->CRITERION_PAYOLUTION_COMPANY_NAME = $CRITERION_PAYOLUTION_COMPANY_NAME;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCOMPANYUID()
    {
        return $this->CRITERION_PAYOLUTION_COMPANY_UID;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_COMPANY_UID
     */
    public function setCRITERIONPAYOLUTIONCOMPANYUID($CRITERION_PAYOLUTION_COMPANY_UID)
    {
        $this->CRITERION_PAYOLUTION_COMPANY_UID = $CRITERION_PAYOLUTION_COMPANY_UID;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONPRECHECKID()
    {
        return $this->CRITERION_PAYOLUTION_PRE_CHECK_ID;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_PRE_CHECK_ID
     */
    public function setCRITERIONPAYOLUTIONPRECHECKID($CRITERION_PAYOLUTION_PRE_CHECK_ID)
    {
        $this->CRITERION_PAYOLUTION_PRE_CHECK_ID = $CRITERION_PAYOLUTION_PRE_CHECK_ID;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONITEMARRAY()
    {
        return $this->CRITERION_PAYOLUTION_ITEM_ARRAY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_ITEM_ARRAY
     */
    public function setCRITERIONPAYOLUTIONITEMARRAY($CRITERION_PAYOLUTION_ITEM_ARRAY)
    {
        $this->CRITERION_PAYOLUTION_ITEM_ARRAY = $CRITERION_PAYOLUTION_ITEM_ARRAY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONCALCULATIONID()
    {
        return $this->CRITERION_PAYOLUTION_CALCULATION_ID;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_CALCULATION_ID
     */
    public function setCRITERIONPAYOLUTIONCALCULATIONID($CRITERION_PAYOLUTION_CALCULATION_ID)
    {
        $this->CRITERION_PAYOLUTION_CALCULATION_ID = $CRITERION_PAYOLUTION_CALCULATION_ID;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONINSTALLMENTAMOUNT()
    {
        return $this->CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT
     */
    public function setCRITERIONPAYOLUTIONINSTALLMENTAMOUNT($CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT)
    {
        $this->CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT = $CRITERION_PAYOLUTION_INSTALLMENT_AMOUNT;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONDURATION()
    {
        return $this->CRITERION_PAYOLUTION_DURATION;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_DURATION
     */
    public function setCRITERIONPAYOLUTIONDURATION($CRITERION_PAYOLUTION_DURATION)
    {
        $this->CRITERION_PAYOLUTION_DURATION = $CRITERION_PAYOLUTION_DURATION;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONACCOUNTHOLDER()
    {
        return $this->CRITERION_PAYOLUTION_ACCOUNT_HOLDER;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_ACCOUNT_HOLDER
     */
    public function setCRITERIONPAYOLUTIONACCOUNTHOLDER($CRITERION_PAYOLUTION_ACCOUNT_HOLDER)
    {
        $this->CRITERION_PAYOLUTION_ACCOUNT_HOLDER = $CRITERION_PAYOLUTION_ACCOUNT_HOLDER;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONACCOUNTCOUNTRY()
    {
        return $this->CRITERION_PAYOLUTION_ACCOUNT_COUNTRY;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_ACCOUNT_COUNTRY
     */
    public function setCRITERIONPAYOLUTIONACCOUNTCOUNTRY($CRITERION_PAYOLUTION_ACCOUNT_COUNTRY)
    {
        $this->CRITERION_PAYOLUTION_ACCOUNT_COUNTRY = $CRITERION_PAYOLUTION_ACCOUNT_COUNTRY;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONACCOUNTBIC()
    {
        return $this->CRITERION_PAYOLUTION_ACCOUNT_BIC;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_ACCOUNT_BIC
     */
    public function setCRITERIONPAYOLUTIONACCOUNTBIC($CRITERION_PAYOLUTION_ACCOUNT_BIC)
    {
        $this->CRITERION_PAYOLUTION_ACCOUNT_BIC = $CRITERION_PAYOLUTION_ACCOUNT_BIC;
    }

    /**
     * @return mixed
     */
    public function getCRITERIONPAYOLUTIONACCOUNTIBAN()
    {
        return $this->CRITERION_PAYOLUTION_ACCOUNT_IBAN;
    }

    /**
     * @param mixed $CRITERION_PAYOLUTION_ACCOUNT_IBAN
     */
    public function setCRITERIONPAYOLUTIONACCOUNTIBAN($CRITERION_PAYOLUTION_ACCOUNT_IBAN)
    {
        $this->CRITERION_PAYOLUTION_ACCOUNT_IBAN = $CRITERION_PAYOLUTION_ACCOUNT_IBAN;
    }
}
