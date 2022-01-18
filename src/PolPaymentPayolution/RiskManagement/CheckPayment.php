<?php

namespace PolPaymentPayolution\RiskManagement;

use Payolution\Config\AbstractConfig;
use Payolution\Request\B2B\RequestBuilder;
use Payolution\Request\Builder\RequestContextFactory;
use Payolution\Request\Builder\RequestOptions;
use Payolution\Request\ELV\PreCheck\ELVPreCheck;
use Payolution\Request\PreCheck\CreatePostParams as PreCheckCreatePostParam;
use Payolution\Request\ELV\PreCheck\CreatePostParams as ElvPreCheckCreatePostParam;
use Payolution\Request\PreCheck\PreCheckPayment;
use Payolution\Request\RequestWrapper;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Request\CreateRequestArray;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContext;
use PolPaymentPayolution\Util\Session\SessionManagerInterface;

/**
 * Class CheckPayment
 *
 * @package PolPaymentPayolution\RiskManagement
 */
class CheckPayment
{
    /**
     * @var AbstractConfig
     */
    private $payolutionConfig;

    /**
     * static Basket Amount with shippingcosts
     * @var $basketAmount null
     */
    public static $basketAmount = null;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * The request wrapper
     *
     * @var RequestWrapper
     */
    private $requestWrapper;

    /**
     * The factory for request context
     *
     * @var RequestContextFactory
     */
    private $requestContextFactory;

    /**
     * The b2b request builder
     *
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * Creator for request array
     *
     * @var CreateRequestArray
     */
    private $requestArrayCreator;

    /**
     * constructor class
     *
     * @param AbstractConfig $payolutionConfig
     * @param ComponentManagerInterface $componentManager
     * @param SessionManagerInterface $session
     * @param RequestWrapper $requestWrapper
     * @param RequestContextFactory $requestContextFactory
     * @param RequestBuilder $requestBuilder
     * @param CreateRequestArray $createRequestArray
     */
    public function __construct(
        AbstractConfig $payolutionConfig,
        ComponentManagerInterface $componentManager,
        SessionManagerInterface $session,
        RequestWrapper $requestWrapper,
        RequestContextFactory $requestContextFactory,
        RequestBuilder $requestBuilder,
        CreateRequestArray $createRequestArray
    ) {
        $this->payolutionConfig = $payolutionConfig;
        $this->componentManager = $componentManager;
        $this->session = $session;
        $this->requestWrapper = $requestWrapper;
        $this->requestContextFactory = $requestContextFactory;
        $this->requestBuilder = $requestBuilder;
        $this->requestArrayCreator = $createRequestArray;
    }

    /**
     * create Request Array from Shopware Variables
     *
     *
     * Parameter Property:
     *             $this->parameters = array(
     *               0 => $user['additional']['user']['paymentID'],
     *               1 => $basket,
     *               2 => $user
     *             );
     *
     *
     * @param $parameter
     * @return bool
     */
    public function checkPayment($parameter)
    {
        if (($parameter[2]['payolution']['mode'] = $this->getPaymentShortName($parameter[0])) !== false) {
            if ($parameter[2]['payolution']['mode'] === 'PAYOLUTION_INS') {
                return false;
            }
            $payolutionPreCheck = Shopware()->Db()->fetchRow(
                'SELECT
                  addressHash,
                  decline,
                  basketValue,
                  lastCheck
                FROM
                  bestit_payolution_userCheck
                WHERE
                  userId = :userId
                AND
                  paymentId = :paymentId',
                array(
                    ':userId' => $parameter[2]['additional']['user']['id'],
                    ':paymentId' => $parameter[0]
                )
            );

            $addressHash = md5(
                json_encode($parameter[2]['billingaddress'])
                .json_encode($parameter[2]['shippingaddress'])
                .Shopware()->Shop()->getCurrency()->getId()
                .Shopware()->Shop()->getId()
            );

            if ($parameter[2]['payolution']['mode'] === 'PAYOLUTION_ELV') {
                $parameter[2]['payolution_elv'] = Shopware()->Db()->fetchRow(
                    'SELECT
                      *
                    FROM
                      bestit_payolution_elv
                    WHERE
                      userId = :userId',
                    array(
                        ':userId' => $parameter[2]['additional']['user']['id'],
                    )
                );
                $addressHash = md5($addressHash.json_encode($parameter[2]['payolution_elv']));
            }

            $taxFree = Shopware()->Db()->fetchOne(
                'SELECT
                  taxfree
                FROM
                  s_core_countries
                WHERE
                  id = :countryId',
                array(
                    ':countryId' => $parameter[2]['additional']['country']['id']
                )
            );

            if (empty($parameter[1]['AmountWithTaxNumeric']) || !isset($parameter[1]['AmountWithTaxNumeric'])) {
                $parameter[1]['AmountWithTaxNumeric'] = $parameter[1]['AmountNumeric'];
            }

            if ($taxFree == 1) {
                $parameter[1]['AmountWithTaxNumeric'] = $parameter[1]['AmountNetNumeric'];
            }

            $AmountNumeric = (string) $parameter[1]['AmountWithTaxNumeric'];

            if ($payolutionPreCheck['addressHash'] != $addressHash
                || $payolutionPreCheck['basketValue'] != $AmountNumeric) {

                $requestWrapper = $this->requestWrapper;

                $user = $parameter[2];
                $basket = $parameter[1];
                $mode = $parameter[2]['payolution']['mode'];
                // Do B2B Pre Check Payment
                if ($mode === 'PAYOLUTION_INVOICE_B2B') {

                    $context = $this->requestContextFactory->create($user);

                    $requestOptions = new RequestOptions(
                        $basket,
                        $user,
                        filter_var($taxFree, FILTER_VALIDATE_BOOLEAN),
                        true
                    );
                    $b2bRequestBuilder = $this->requestBuilder;
                    $postData = $b2bRequestBuilder->buildRequest($requestOptions, $context);
                } else {
                    $requestParams = $this->requestArrayCreator->createArray($basket, $user, true, $mode, $taxFree);

                    if ($mode === 'PAYOLUTION_ELV') {
                        $data = new ELVPreCheck();
                    } else {
                        $data = new PreCheckPayment();
                    }

                    foreach ($requestParams as $method => $value) {
                        $data->$method($value);
                    }

                    if ($mode === 'PAYOLUTION_ELV') {
                        $postData = ElvPreCheckCreatePostParam::createParams($data, $this->payolutionConfig);

                    } else {
                        $postData = PreCheckCreatePostParam::createParams($data, $this->payolutionConfig);
                    }
                }

                $return = $requestWrapper->doRequest($postData);


                $sql = '
                REPLACE INTO
                 bestit_payolution_userCheck
                (
                `userId`,
                `paymentId`,
                `decline`,
                `addressHash`,
                `uniqueId`,
                `lastCheck`,
                `basketValue`
                )
                VALUES
                (
                :userId,
                :paymentId,
                :decline,
                :addressHash,
                :uniqueId,
                :lastCheck,
                :basketValue
                )';

                $params = array(
                    ':userId' => $parameter[2]['additional']['user']['id'],
                    ':paymentId' => $parameter[0],
                    ':decline' => 0,
                    ':addressHash' => $addressHash,
                    ':uniqueId' => $return['IDENTIFICATION_UNIQUEID'],
                    ':lastCheck' => time(),
                    ':basketValue' => $AmountNumeric
                );

                if ($return['PROCESSING_STATUS_CODE'] == '90' || $return['PROCESSING_STATUS_CODE'] == '00') {
                    Shopware()->Db()->query($sql, $params);
                    return true;
                } else {
                    $params[':decline'] = 1;
                    Shopware()->Db()->query($sql, $params);
                    return array(
                        'payment' => $parameter[2]['payolution']['mode'],
                        'message' => 'rejected',
                    );
                }
            } else {
                $decline = Shopware()->Db()->fetchOne(
                    'SELECT
                      decline
                    FROM
                      bestit_payolution_userCheck
                    WHERE
                      userId = :userId
                    AND
                      paymentId = :paymentId',
                    array(
                        ':userId' => $parameter[2]['additional']['user']['id'],
                        ':paymentId' => $parameter[0],
                    )
                );

                if ($decline == 1) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    /**
     * check if customer is merchant
     *
     * @param RiskManagementContext $context
     *
     * @return bool
     */
    public function checkCustomer(RiskManagementContext $context)
    {
        $paymentId = $context->getPaymentId();
        $user = $context->getUser();

        if (empty($user['billingaddress']['firstname'])) {
            return true;
        }

        $paymentName = $this->getPaymentShortName($paymentId);

        if (!$paymentName) {
            return false;
        }

        $payments = [
            'PAYOLUTION_INVOICE',
            'PAYOLUTION_INS',
            'PAYOLUTION_ELV'
        ];

        $hasCompany = !empty($user['billingaddress']['company']);

        // Check payolution payment for non b2b customers
        if ($hasCompany && in_array($paymentName, $payments, true)) {
            return true;
        } elseif ($paymentName === 'PAYOLUTION_INVOICE_B2B' && !$hasCompany) {
            return true;
        }

        $basketValue = $this->checkBasketValue($context);

        if ($basketValue) {
            return true;
        }
    }


    /**
     * check if user has different addresses
     *
     * @param $parameter
     * @return mixed
     */
    public function checkDifferentAddresses($parameter)
    {
        if (!empty($parameter[2]['billingaddress']['firstname'])) {
            if (($payment = $this->getPaymentShortName($parameter[0])) !== false) {
                if (!$this->payolutionConfig->isDifferentAddresses()) {
                    $billingAddress = array(
                        $parameter[2]['billingaddress']['firstname'],
                        $parameter[2]['billingaddress']['lastname'],
                        $parameter[2]['billingaddress']['salutation'],
                        $parameter[2]['billingaddress']['street'],
                        $parameter[2]['billingaddress']['zipcode'],
                        $parameter[2]['billingaddress']['city'],
                        $parameter[2]['billingaddress']['countryID'],
                        $parameter[2]['billingaddress']['company'],
                        $parameter[2]['billingaddress']['department'],
                    );
                    $shippingAddress = array(
                        $parameter[2]['shippingaddress']['firstname'],
                        $parameter[2]['shippingaddress']['lastname'],
                        $parameter[2]['shippingaddress']['salutation'],
                        $parameter[2]['shippingaddress']['street'],
                        $parameter[2]['shippingaddress']['zipcode'],
                        $parameter[2]['shippingaddress']['city'],
                        $parameter[2]['shippingaddress']['countryID'],
                        $parameter[2]['shippingaddress']['company'],
                        $parameter[2]['shippingaddress']['department'],
                    );
                    $diff = array_diff($billingAddress, $shippingAddress);
                    if (!empty($diff)) {
                        return array(
                            'payment' => $payment,
                            'message' => 'address'
                        );
                    }
                }
            }
        }
    }

    /**
     * Check if basket is filled.
     *
     * @param RiskManagementContext $context
     *
     * @return bool If the customer is a risk.
     */
    public function checkBasketValue(RiskManagementContext $context)
    {
        $paymentId = $context->getPaymentId();
        $basket = $context->getBasket();
        $user = $context->getUser();

        if (empty($user['billingaddress']['firstname'])) {
            return false;
        }

        $payment = $this->getPaymentShortName($paymentId);

        if (!$payment) {
            return false;
        }

        if (empty($basket)) {
            return true;
        }

        $this->session->set('payolutionSkipRiskManagement', true);
        if (self::$basketAmount === null) {
            $shippingcosts = $this->componentManager->getAdminModule()->sGetPremiumShippingcosts();
            self::$basketAmount = $basket['AmountNumeric'] + $shippingcosts['brutto'];
        }
        $this->session->set('payolutionSkipRiskManagement', false);

        list($minValue, $maxValue) = $this->getPaymentBasketMinMaxValue($payment);

        if ((string) self::$basketAmount < $minValue || (string) self::$basketAmount > $maxValue) {
            return true;
        }
    }

    /**
     * check if basket is filled
     *
     * @param $parameter
     * @return bool
     */
    public function checkRestrictions($parameter)
    {
        if (!empty($parameter[2]['billingaddress']['firstname'])) {
            if (($payment = $this->getPaymentShortName($parameter[0])) !== false) {
                if (empty($parameter[1])) {
                    return true;
                }

                switch ($payment) {
                    case 'PAYOLUTION_INVOICE':
                        $countries = array_map('trim', explode(',', $this->payolutionConfig->getAllowedCountriesInvoiceB2C()));
                        break;
                    case 'PAYOLUTION_INVOICE_B2B':
                        $countries = array_map('trim', explode(',', $this->payolutionConfig->getAllowedCountriesInvoiceB2B()));
                        break;
                    case 'PAYOLUTION_INS':
                        $countries = array_map('trim', explode(',', $this->payolutionConfig->getAllowedCountriesInstallment()));
                        break;
                    case 'PAYOLUTION_ELV':
                        $countries = array_map('trim', explode(',', $this->payolutionConfig->getAllowedCountriesElv()));
                        break;
                    default:
                        $countries = array_map('trim', explode(',', $this->payolutionConfig->getAllowedCountriesInvoiceB2C()));
                }

                $currencies = array_map('trim', explode(',', $this->payolutionConfig->getAllowedCurrencies()));

                if (!in_array(Shopware()->Shop()->getCurrency()->getCurrency(), $currencies)) {
                    return array(
                        'payment' => $payment,
                        'message' => 'currency'
                    );
                }

                if (!in_array($parameter[2]['additional']['country']['countryiso'], $countries)) {
                    return array(
                        'payment' => $payment,
                        'message' => 'country'
                    );
                }
            }
        }
    }

    /**
     * get the shortName of the Payment
     *
     * @param $paymentId
     *
     * @return bool|string
     */
    private function getPaymentShortName($paymentId)
    {
        $paymentName = Shopware()->db()->fetchOne(
            'SELECT
              name
            FROM
              s_core_paymentmeans
            WHERE
              id = :paymentId',
            array(
                ':paymentId' => $paymentId
            )
        );

        switch ($paymentName) {
            case 'payolution_invoice_b2c':
                return 'PAYOLUTION_INVOICE';
            case 'payolution_invoice_b2b':
                return 'PAYOLUTION_INVOICE_B2B';
            case 'payolution_installment':
                return 'PAYOLUTION_INS';
            case 'payolution_elv':
                return 'PAYOLUTION_ELV';
            default:
                return false;
        }
    }

    /**
     * Gets the basket min and max values for the given payment name.
     *
     * @param $payment
     *
     * @return array
     */
    private function getPaymentBasketMinMaxValue($payment)
    {
        $minValue = 0;
        $maxValue = 99999999999;

        switch ($payment) {
            case 'PAYOLUTION_INVOICE':
                $minValue = $this->payolutionConfig->getMinB2cValue();
                $maxValue = $this->payolutionConfig->getMaxB2cValue();
                break;
            case 'PAYOLUTION_INVOICE_B2B':
                $minValue = $this->payolutionConfig->getMinB2bValue();
                $maxValue = $this->payolutionConfig->getMaxB2bValue();
                break;
            case 'PAYOLUTION_INS':
                $minValue = $this->payolutionConfig->getMinInstallmentValue();
                $maxValue = $this->payolutionConfig->getMaxInstallmentValue();
                break;
            case 'PAYOLUTION_ELV':
                $minValue = $this->payolutionConfig->getMinElvValue();
                $maxValue = $this->payolutionConfig->getMaxElvValue();
                break;
        }

        $minValue = str_replace(',', '.', $minValue);
        $maxValue = str_replace(',', '.', $maxValue);
        $minValue = (!empty($minValue) ? $minValue : '0');
        $maxValue = (!empty($maxValue) ? $maxValue : '99999999999');

        return [$minValue, $maxValue];
    }
}
