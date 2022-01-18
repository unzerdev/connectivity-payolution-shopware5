<?php

namespace Payolution\Request\Builder\Mapper;

use Exception;
use Payolution\Config\Config;
use Payolution\Request\Builder\RequestContext;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use Shopware;
use Shopware\Models\Shop\Shop;

/**
 * Mapper for system attributes
 *
 * @package Payolution\Request\Builder\Mapper
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SystemMapper
{
    /**
     * The plugin name
     *
     * @var string
     */
    private $pluginName;

    /**
     * The plugin version
     *
     * @var string
     */
    private $pluginVersion;

    /**
     * The shopware version
     *
     * @var string
     */
    private $shopwareVersion;

    /**
     * SystemMapper constructor.
     *
     * @param string $pluginName
     * @param string $pluginVersion
     * @param string $shopwareVersion
     */
    public function __construct($pluginName, $pluginVersion, $shopwareVersion)
    {
        $this->pluginName = $pluginName;
        $this->pluginVersion = $pluginVersion;
        $this->shopwareVersion = $shopwareVersion;
    }

    /**
     * Map Request
     *
     * @param RequestContext $context
     * @param array $request
     *
     * @return void
     *
     * @throws Exception
     */
    public function mapRequest(RequestContext $context, array &$request)
    {
        $config = $context->getConfig();

        $this->mapBaseRequest($config, $context->getShop(), $request);

        $request['ACCOUNT.BRAND'] = 'PAYOLUTION_INVOICE';
        $request['IDENTIFICATION.TRANSACTIONID'] = $context->getTransactionId();
        $request['PAYMENT.CODE'] = 'VA.PA';
    }

    /**
     * Map Base Request
     *
     * @param Config $config
     * @param Shop $shop
     * @param array $request
     *
     * @return void
     *
     * @throws Exception
     */
    private function mapBaseRequest(Config $config, Shop $shop, array &$request)
    {
        $request['SECURITY.SENDER'] = $config->getSender();
        $request['USER.LOGIN'] = $config->getLogin();
        $request['USER.PWD'] = $config->getPasswd();
        $request['TRANSACTION.MODE'] = $config->isTestmode() ? 'CONNECTOR_TEST' : 'LIVE';
        $request['TRANSACTION.RESPONSE'] = 'SYNC';
        $request['REQUEST.VERSION'] = '1.0';
        $request['CRITERION.PAYOLUTION_REQUEST_SYSTEM_VENDOR'] = 'Shopware_PHP_POST';
        $request['CRITERION.PAYOLUTION_REQUEST_SYSTEM_VERSION'] = $this->shopwareVersion;
        $request['CRITERION.PAYOLUTION_REQUEST_SYSTEM_TYPE'] = 'Webshop';
        $request['CRITERION.PAYOLUTION_WEBSHOP_URL'] = $shop->getHost();
        $request['CRITERION.PAYOLUTION_MODULE_NAME'] =  $this->pluginName;
        $request['CRITERION.PAYOLUTION_MODULE_VERSION'] =  $this->pluginVersion;
    }

    /**
     * Map Capture Request
     *
     * @param WorkflowElementsContext $context
     * @param array $request
     *
     * @return void
     *
     * @throws Exception
     */
    public function mapCaptureRequest(WorkflowElementsContext $context, array &$request)
    {
        $this->mapBaseRequest($context->getConfig(), $context->getShop(), $request);
        $request['PAYMENT.CODE'] = 'VA.CP';
    }

    /**
     * Map Refund Request
     *
     * @param WorkflowElementsContext $context
     * @param array $request
     *
     * @return void
     *
     * @throws Exception
     */
    public function mapRefundRequest(WorkflowElementsContext $context, array &$request)
    {
        $this->mapBaseRequest($context->getConfig(), $context->getShop(), $request);
        $request['PAYMENT.CODE'] = 'VA.RF';
    }
}
