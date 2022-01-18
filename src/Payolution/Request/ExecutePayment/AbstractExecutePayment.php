<?php
namespace Payolution\Request\ExecutePayment;

abstract class AbstractExecutePayment
{

    private $REQUEST_VERSION;
    private $TRANSACTION_RESPONSE;
    private $PAYMENT_CODE;
    private $ACCOUNT_BRAND;
    private $IDENTIFICATION_TRANSACTIONID;
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
    private $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_DATE;
    private $CRITERION_PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL;
    private $PAYOLUTION_PAYMENT_MODE;
    private $CRITERION_PAYOLUTION_ITEM_ARRAY;
    private $IDENTIFICATION_REFERENCEID;


    abstract public function getCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONDATE();

    abstract public function getCRITERIONPAYOLUTIONCUSTOMERREGISTRATIONLEVEL();

    abstract public function getREQUESTVERSION();

    abstract public function getIDENTIFICATIONREFERENCEID();

    abstract public function getPAYOLUTIONPAYMENTMODE();

    abstract public function getTRANSACTIONRESPONSE();

    abstract public function getPAYMENTCODE();

    abstract public function getACCOUNTBRAND();

    abstract public function getIDENTIFICATIONTRANSACTIONID();

    abstract public function getIDENTIFICATIONSHOPPERID();

    abstract public function getPRESENTATIONAMOUNT();

    abstract public function getPRESENTATIONCURRENCY();

    abstract public function getPRESENTATIONUSAGE();

    abstract public function getNAMESEX();

    abstract public function getNAMEGIVEN();

    abstract public function getNAMEFAMILY();

    abstract public function getNAMEBIRTHDATE();

    abstract public function getCONTACTEMAIL();

    abstract public function getCONTACTPHONE();

    abstract public function getCONTACTIP();

    abstract public function getADDRESSSTREET();

    abstract public function getADDRESSZIP();

    abstract public function getADDRESSCITY();

    abstract public function getADDRESSCOUNTRY();

    abstract public function getCRITERIONPAYOLUTIONCUSTOMERGROUP();

    abstract public function getCRITERIONPAYOLUTIONCUSTOMERLANGUAGE();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGCOMPANY();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGGIVEN();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGFAMILY();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGCOUNTRY();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGSTREET();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGZIP();

    abstract public function getCRITERIONPAYOLUTIONSHIPPINGCITY();

    abstract public function getCRITERIONPAYOLUTIONREQUESTSYSTEMVENDOR();

    abstract public function getCRITERIONPAYOLUTIONREQUESTSYSTEMVERSION();

    abstract public function getCRITERIONPAYOLUTIONREQUESTSYSTEMTYPE();

    abstract public function getCRITERIONPAYOLUTIONWEBSHOPURL();

    abstract public function getCRITERIONPAYOLUTIONMODULENAME();

    abstract public function getCRITERIONPAYOLUTIONMODULEVERSION();

    abstract public function getCRITERIONPAYOLUTIONTAXAMOUNT();

    abstract public function getCRITERIONPAYOLUTIONCOMPANYNAME();

    abstract public function getCRITERIONPAYOLUTIONCOMPANYUID();

    abstract public function getCRITERIONPAYOLUTIONPRECHECKID();

    abstract public function getCRITERIONPAYOLUTIONITEMARRAY();
}
