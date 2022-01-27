<?php

namespace Payolution\Request\Builder;

use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Payolution\Config\Config;
use Payolution\Config\ConfigLoader;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Shopware\Models\Order\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RequestContextFactory
 *
 * @package Payolution\Request\Builder
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RequestContextFactory
{
    use UniqueNumberTrait;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var ConfigLoader
     */
    private $loader;

    /**
     * @var Config;
     */
    private $config;

    /**
     * RequestContextFactory constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param ConfigLoader $loader
     */
    public function __construct(ComponentManagerInterface $componentManager, ConfigLoader $loader)
    {
        $this->componentManager = $componentManager;
        $this->loader = $loader;
    }

    /**
     * Create Context
     *
     * @return RequestContext
     */
    public function create()
    {
        return new RequestContext(
            $this->loader->getCurrentShop(),
            $this->loader->loadCurrentConfig(),
            $this->generateUniqueId(),
            $this->generateUniqueId()
        );
    }

    /**
     * Create with Transaction Infos
     *
     * @param string $transActionId
     * @param string $trxId
     * @param string $referenceId
     *
     * @return RequestContext
     */
    public function createWithTransactionInfos($transActionId, $trxId, $referenceId)
    {
        return new RequestContext(
            $this->loader->getCurrentShop(),
            $this->loader->loadCurrentConfig(),
            $transActionId,
            $trxId,
            $referenceId
        );
    }
}
