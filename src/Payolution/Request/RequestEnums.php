<?php

namespace Payolution\Request;

/**
 * Class RequestEnums
 *
 * @package Payolution\Request
 */
final class RequestEnums
{
    const CI_TEST_URL = 'test-payment.payolution.com';

    const CI_PROD_URL = 'payment.payolution.com';

    const CI_TEST_ENDPOINT = '/payolution-payment/rest/request/v2';

    const CI_ENDPOINT = '/payolution-payment/rest/request/v2';

    const PAYMENT_TEST_URL = 'test-gateway.payolution.com/';

    const PAYMENT_PROD_URL = 'gateway.payolution.com/';

    const PAYMENT_ENDPOINT = 'ctpe/post';

    const CI_TYPE = 'ci_type';

    const REQUEST_TYPE = 'request_type';

    /**
     * RequestEnums constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get Test Endpoint
     *
     * @param bool $ci
     * @return string
     */
    public static function getTestEndPoint($ci = false)
    {
        if ($ci) {
            return 'https://' . self::CI_TEST_URL . self::CI_TEST_ENDPOINT;
        }

        return 'https://' . self::PAYMENT_TEST_URL . self::PAYMENT_ENDPOINT;
    }

    /**
     * Get Prod Endpoint
     *
     * @param bool $ci
     * @return string
     */
    public static function getProdEndpoint($ci = false)
    {
        if ($ci) {
            return 'https://' . self::CI_PROD_URL . self::CI_ENDPOINT;
        }

        return 'https://' . self::PAYMENT_PROD_URL . self::PAYMENT_ENDPOINT;
    }
}
