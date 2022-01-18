<?php

namespace Payolution\Request\Builder;

use Payolution\Config\Config;
use Shopware\Models\Shop\DetachedShop;

/**
 * Class RequestContext
 *
 * @package Payolution\Request\Builder
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RequestContext
{
    /**
     * @var DetachedShop
     */
    private $shop;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $transactionId;

    /**
     * @var string
     */
    private $trxId;

    /**
     * @var string
     */
    private $preCheckId;

    /**
     * @var string
     */
    private $referenceId;

    /**
     * RequestContext constructor.
     *
     * @param DetachedShop $shop
     * @param Config $config
     * @param $transactionId
     * @param $trxId
     * @param null $preCheckId
     * @param null $referenceId
     */
    public function __construct(
        DetachedShop $shop,
        Config $config,
        $transactionId,
        $trxId = null,
        $preCheckId = null,
        $referenceId = null
    ) {
        $this->shop = $shop;
        $this->config = $config;
        $this->transactionId = $transactionId;
        $this->trxId = $trxId;
        $this->preCheckId = $preCheckId;
        $this->referenceId = $referenceId;
    }

    /**
     * Get Shop
     *
     * @return DetachedShop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Get Config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get TransactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Get TrxId
     *
     * @return string
     */
    public function getTrxId()
    {
        return $this->trxId;
    }

    /**
     * Get PreCheckId
     *
     * @return string|null
     */
    public function getPreCheckId()
    {
        return $this->preCheckId;
    }

    /**
     * Get ReferenceId
     *
     * @return string|null
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }
}

