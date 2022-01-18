<?php

namespace Payolution\Request\Builder\Mapper;

use Payolution\Request\Builder\RequestContext;
use Payolution\Request\Builder\RequestOptions;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserMapper
 *
 * @package Payolution\Request\Builder\Mapper
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class UserMapper
{
    /**
     * Map Request
     *
     * @param RequestOptions $options
     * @param RequestContext $context
     * @param array $request
     *
     * @return void
     */
    public function mapRequest(RequestOptions $options, RequestContext $context, array &$request)
    {
        $user = $options->getUser();
        $request['NAME.SEX'] = $user['billingaddress']['salutation'] === 'mr' ? 'M' : 'F';
        $request['NAME.GIVEN'] = $user['billingaddress']['firstname'];
        $request['NAME.FAMILY'] = $user['billingaddress']['lastname'];
        $request['NAME.BIRTHDATE'] = $user['additional']['user']['birthday'];
        $request['CONTACT.EMAIL'] = $user['additional']['user']['email'];
        $request['CONTACT.PHONE'] = $user['billingaddress']['phone'];
        $request['CONTACT.IP'] = Request::createFromGlobals()->getClientIp();
        $request['ADDRESS.STREET'] = $user['billingaddress']['street'];
        $request['ADDRESS.ZIP'] = $user['billingaddress']['zipcode'];
        $request['ADDRESS.CITY'] = $user['billingaddress']['city'];
        $request['ADDRESS.COUNTRY'] = $user['additional']['country']['countryiso'];
        $request['CRITERION.PAYOLUTION_COMPANY_NAME'] = $user['billingaddress']['company'];
        $request['CRITERION.PAYOLUTION_COMPANY_UID'] =  $user['billingaddress']['ustid'];
        $request['CRITERION.PAYOLUTION_CUSTOMER_GROUP'] =  $user['additional']['user']['customergroup'];
        $request['CRITERION.PAYOLUTION_CUSTOMER_LANGUAGE'] =substr($context->getShop()->getLocale()->getLocale(), 0, 2);
        $request['CRITERION.PAYOLUTION_CUSTOMER_REGISTRATION_LEVEL'] =
            (int) $user['additional']['user']['accountmode'] === 0 ? 1 : 0;
        $request['CRITERION.PAYOLUTION_CUSTOMER_REGISTRATION_DATE'] =
            str_replace('-', '', $user['additional']['user']['firstlogin']);
        $request['CRITERION.PAYOLUTION_SHIPPING_GIVEN'] = $user['shippingaddress']['firstname'];
        $request['CRITERION.PAYOLUTION_SHIPPING_FAMILY'] = $user['shippingaddress']['lastname'];
        $request['CRITERION.PAYOLUTION_SHIPPING_COUNTRY'] =  $user['additional']['country']['countryiso'];
        $request['CRITERION.PAYOLUTION_SHIPPING_STREET'] = $user['shippingaddress']['street'];
        $request['CRITERION.PAYOLUTION_SHIPPING_ZIP'] = $user['shippingaddress']['zipcode'];
        $request['CRITERION.PAYOLUTION_SHIPPING_CITY'] = $user['shippingaddress']['city'];
        $request['CRITERION.PAYOLUTION_SHIPPING_COMPANY'] = $user['shippingaddress']['company'];
        $request['IDENTIFICATION.SHOPPERID'] = $user['additional']['user']['customernumber'];
    }
}