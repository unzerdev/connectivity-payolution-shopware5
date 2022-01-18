<?php
namespace Payolution\Config;

abstract class AbstractConfig
{

    private $mail_bcc;
    private $channel_invoice;
    private $channel_installment;
    private $channel_b2b_invoice;
    private $channel_elv;
    private $channel_precheck;
    private $login;
    private $sender;
    private $passwd;
    private $iban;
    private $bic;
    private $testmode = true;
    private $logging = true;
    private $min_installment_value;
    private $installment_payolution_user;
    private $installment_payolution_password;
    private $holder;
    private $max_installment_value;
    private $min_b2b_value;
    private $max_b2b_value;
    private $min_b2c_value;
    private $max_b2c_value;
    private $min_elv_value;
    private $max_elv_value;
    private $min_installment_detail_value;
    private $max_installment_detail_value;
    private $differentAddresses;
    private $company;
    private $allowedCountriesInvoiceB2C;
    private $allowedCountriesInvoiceB2B;
    private $allowedCountriesInstallment;
    private $allowedCountriesElv;
    private $allowedCurrencies;

    abstract public function getMinElvValue();

    abstract public function getMaxElvValue();

    abstract public function getAllowedCountriesInvoiceB2C();

    abstract public function getAllowedCountriesInvoiceB2B();

    abstract public function getAllowedCountriesInstallment();

    abstract public function getAllowedCountriesElv();

    abstract public function getAllowedCurrencies();

    abstract public function getCompany();

    abstract public function getMinInstallmentDetailValue();

    abstract public function getMaxInstallmentDetailValue();

    abstract public function isDifferentAddresses();

    abstract public function getMaxInstallmentValue();

    abstract public function getMinB2bValue();

    abstract public function getMaxB2bValue();

    abstract public function getMinB2cValue();

    abstract public function getMaxB2cValue();

    abstract public function getInstallmentPayolutionUser();

    abstract public function getInstallmentPayolutionPassword();

    abstract public function getHolder();

    abstract public function getMailBcc();

    abstract public function getMinInstallmentValue();

    abstract public function getChannelInvoice();

    abstract public function getChannelElv();

    abstract public function getChannelInstallment();

    abstract public function getChannelB2bInvoice();

    abstract public function getChannelPrecheck();

    abstract public function getLogin();

    abstract public function getSender();

    abstract public function getPasswd();

    abstract public function getIban();

    abstract public function getBic();

    abstract public function isTestmode();

    abstract public function isLogging();

    abstract public function getConfig();
}
