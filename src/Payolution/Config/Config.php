<?php
namespace Payolution\Config;

class Config extends AbstractConfig
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
    private $testmode;
    private $logging;
    private $min_installment_value;
    private $max_installment_value;
    private $min_b2b_value;
    private $max_b2b_value;
    private $min_b2c_value;
    private $max_b2c_value;
    private $min_elv_value;
    private $max_elv_value;
    private $installment_payolution_user;
    private $installment_payolution_password;
    private $holder;
    private $min_installment_detail_value;
    private $max_installment_detail_value;
    private $differentAddresses;
    private $company;
    private $allowedCountriesInvoiceB2C;
    private $allowedCountriesInvoiceB2B;
    private $allowedCountriesInstallment;
    private $allowedCountriesElv;
    private $allowedCurrencies;

    /**
     * @return mixed
     */
    public function getMinElvValue()
    {
        return $this->min_elv_value;
    }

    /**
     * @param mixed $min_elv_value
     */
    public function setMinElvValue($min_elv_value)
    {
        $this->min_elv_value = $min_elv_value;
    }

    /**
     * @return mixed
     */
    public function getMaxElvValue()
    {
        return $this->max_elv_value;
    }

    /**
     * @param mixed $max_elv_value
     */
    public function setMaxElvValue($max_elv_value)
    {
        $this->max_elv_value = $max_elv_value;
    }

    /**
     * @return mixed
     */
    public function getAllowedCountriesInvoiceB2C()
    {
        return $this->allowedCountriesInvoiceB2C;
    }

    /**
     * @param mixed $allowedCountriesInvoiceB2C
     */
    public function setAllowedCountriesInvoiceB2C($allowedCountriesInvoiceB2C)
    {
        $this->allowedCountriesInvoiceB2C = $allowedCountriesInvoiceB2C;
    }

    /**
     * @return mixed
     */
    public function getAllowedCountriesInvoiceB2B()
    {
        return $this->allowedCountriesInvoiceB2B;
    }

    /**
     * @param mixed $allowedCountriesInvoiceB2B
     */
    public function setAllowedCountriesInvoiceB2B($allowedCountriesInvoiceB2B)
    {
        $this->allowedCountriesInvoiceB2B = $allowedCountriesInvoiceB2B;
    }

    /**
     * @return mixed
     */
    public function getAllowedCountriesInstallment()
    {
        return $this->allowedCountriesInstallment;
    }

    /**
     * @param mixed $allowedCountriesInstallment
     */
    public function setAllowedCountriesInstallment($allowedCountriesInstallment)
    {
        $this->allowedCountriesInstallment = $allowedCountriesInstallment;
    }

    /**
     * @return mixed
     */
    public function getAllowedCountriesElv()
    {
        return $this->allowedCountriesElv;
    }

    /**
     * @param mixed $allowedCountriesElv
     */
    public function setAllowedCountriesElv($allowedCountriesElv)
    {
        $this->allowedCountriesElv = $allowedCountriesElv;
    }

    /**
     * @return mixed
     */
    public function getAllowedCurrencies()
    {
        return $this->allowedCurrencies;
    }

    /**
     * @param mixed $allowedCurrencies
     */
    public function setAllowedCurrencies($allowedCurrencies)
    {
        $this->allowedCurrencies = $allowedCurrencies;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getMinInstallmentDetailValue()
    {
        return $this->min_installment_detail_value;
    }

    /**
     * @param mixed $min_installment_detail_value
     */
    public function setMinInstallmentDetailValue($min_installment_detail_value)
    {
        $this->min_installment_detail_value = $min_installment_detail_value;
    }

    /**
     * @return mixed
     */
    public function getMaxInstallmentDetailValue()
    {
        return $this->max_installment_detail_value;
    }

    /**
     * @param mixed $max_installment_detail_value
     */
    public function setMaxInstallmentDetailValue($max_installment_detail_value)
    {
        $this->max_installment_detail_value = $max_installment_detail_value;
    }

    /**
     * @return boolean
     */
    public function isDifferentAddresses()
    {
        return filter_var($this->differentAddresses, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param mixed $differentAddresses
     */
    public function setDifferentAddresses($differentAddresses)
    {
        $this->differentAddresses = $differentAddresses;
    }

    /**
     * @return mixed
     */
    public function getMaxInstallmentValue()
    {
        return $this->max_installment_value;
    }

    /**
     * @param mixed $max_installment_value
     */
    public function setMaxInstallmentValue($max_installment_value)
    {
        $this->max_installment_value = $max_installment_value;
    }

    /**
     * @return mixed
     */
    public function getMinB2bValue()
    {
        return $this->min_b2b_value;
    }

    /**
     * @param mixed $min_b2b_value
     */
    public function setMinB2bValue($min_b2b_value)
    {
        $this->min_b2b_value = $min_b2b_value;
    }

    /**
     * @return mixed
     */
    public function getMaxB2bValue()
    {
        return $this->max_b2b_value;
    }

    /**
     * @param mixed $max_b2b_value
     */
    public function setMaxB2bValue($max_b2b_value)
    {
        $this->max_b2b_value = $max_b2b_value;
    }

    /**
     * @return mixed
     */
    public function getMinB2cValue()
    {
        return $this->min_b2c_value;
    }

    /**
     * @param mixed $min_b2c_value
     */
    public function setMinB2cValue($min_b2c_value)
    {
        $this->min_b2c_value = $min_b2c_value;
    }

    /**
     * @return mixed
     */
    public function getMaxB2cValue()
    {
        return $this->max_b2c_value;
    }

    /**
     * @param mixed $max_b2c_value
     */
    public function setMaxB2cValue($max_b2c_value)
    {
        $this->max_b2c_value = $max_b2c_value;
    }

    /**
     * @return mixed
     */
    public function getInstallmentPayolutionUser()
    {
        return $this->installment_payolution_user;
    }

    /**
     * @param mixed $installment_payolution_user
     */
    public function setInstallmentPayolutionUser($installment_payolution_user)
    {
        $this->installment_payolution_user = $installment_payolution_user;
    }

    /**
     * @return mixed
     */
    public function getInstallmentPayolutionPassword()
    {
        return $this->installment_payolution_password;
    }

    /**
     * @param mixed $installment_payolution_password
     */
    public function setInstallmentPayolutionPassword($installment_payolution_password)
    {
        $this->installment_payolution_password = $installment_payolution_password;
    }

    /**
     * @return mixed
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @param mixed $holder
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;
    }

    public function __construct(array $data = array())
    {

    }

    public function getValue($key)
    {
        return $this->$key;
    }

    public function setValue($key, $value)
    {
        $this->$key = $value;

        return $this;
    }

    protected function set($key, $value, array &$tree)
    {
    }

    public function setConfig($config)
    {
        foreach($config as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    public function getConfig()
    {
        return array(
            'mail_bcc' => $this->mail_bcc,
            'channel_invoice' => $this->channel_invoice,
            'channel_installment' => $this->channel_installment,
            'channel_b2b_invoice' => $this->channel_b2b_invoice,
            'channel_elv' => $this->channel_elv,
            'channel_precheck' => $this->channel_precheck,
            'login' => $this->login,
            'sender' => $this->sender,
            'passwd' => $this->passwd,
            'iban' => $this->iban,
            'bic' => $this->bic,
            'testmode' => $this->testmode,
            'logging' => $this->logging,
            'min_installment_value' => $this->min_installment_value,
            'installment_payolution_user' => $this->installment_payolution_user,
            'installment_payolution_password' => $this->installment_payolution_password,
            'holder' => $this->holder,
            'max_installment_value' => $this->max_installment_value,
            'min_b2b_value' => $this->min_b2b_value,
            'max_b2b_value' => $this->max_b2b_value,
            'min_b2c_value' => $this->min_b2c_value,
            'max_b2c_value' => $this->max_b2c_value,
            'min_elv_value' => $this->min_elv_value,
            'max_elv_value' => $this->max_elv_value,
            'min_installment_detail_value' => $this->min_installment_detail_value,
            'max_installment_detail_value' => $this->max_installment_detail_value,
            'differentAddresses' => $this->differentAddresses,
            'company' => $this->company,
            'allowedCountriesInvoiceB2C' => $this->allowedCountriesInvoiceB2C,
            'allowedCountriesInvoiceB2B' => $this->allowedCountriesInvoiceB2B,
            'allowedCountriesInstallment' => $this->allowedCountriesInstallment,
            'allowedCountriesElv' => $this->allowedCountriesElv,
            'allowedCurrencies' => $this->allowedCurrencies,
        );
    }

    /**
     * @return mixed
     */
    public function getMinInstallmentValue()
    {
        return $this->min_installment_value;
    }

    /**
     * @param mixed $min_installment_value
     */
    public function setMinInstallmentValue($min_installment_value)
    {
        $this->min_installment_value = $min_installment_value;
    }

    /**
     * @return string
     */
    public function getMailBcc()
    {
        return $this->mail_bcc;
    }

    /**
     * @param string $mail_bcc
     *
     * @return AbstractConfig
     */
    public function setMailBcc($mail_bcc)
    {
        $this->mail_bcc = $mail_bcc;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannelInvoice()
    {
        return $this->channel_invoice;
    }

    /**
     * @return mixed
     */
    public function getChannelElv()
    {
        return $this->channel_elv;
    }

    /**
     * @param mixed $channel_elv
     */
    public function setChannelElv($channel_elv)
    {
        $this->channel_elv = $channel_elv;
    }

    /**
     * @param string $channel_invoice
     *
     * @return AbstractConfig
     */
    public function setChannelInvoice($channel_invoice)
    {
        $this->channel_invoice = $channel_invoice;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannelInstallment()
    {
        return $this->channel_installment;
    }

    /**
     * @param string $channel_installment
     *
     * @return AbstractConfig
     */
    public function setChannelInstallment($channel_installment)
    {
        $this->channel_installment = $channel_installment;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannelB2bInvoice()
    {
        return $this->channel_b2b_invoice;
    }

    /**
     * @param string $channel_b2b_invoice
     *
     * @return AbstractConfig
     */
    public function setChannelB2bInvoice($channel_b2b_invoice)
    {
        $this->channel_b2b_invoice = $channel_b2b_invoice;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannelPrecheck()
    {
        return $this->channel_precheck;
    }

    /**
     * @param string $channel_precheck
     *
     * @return AbstractConfig
     */
    public function setChannelPrecheck($channel_precheck)
    {
        $this->channel_precheck = $channel_precheck;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return AbstractConfig
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     *
     * @return AbstractConfig
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd
     *
     * @return AbstractConfig
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     *
     * @return AbstractConfig
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @param string $bic
     *
     * @return AbstractConfig
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTestmode()
    {
        return filter_var($this->testmode, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param boolean $testmode
     *
     * @return AbstractConfig
     */
    public function setTestmode($testmode)
    {
        $this->testmode = $testmode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLogging()
    {
        return filter_var($this->logging, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param boolean $logging
     *
     * @return AbstractConfig
     */
    public function setLogging($logging)
    {
        $this->logging = $logging;

        return $this;
    }
}
