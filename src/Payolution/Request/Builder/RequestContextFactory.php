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
     * @param array $user
     * @return RequestContext
     */
    public function create(array $user)
    {
        return new RequestContext(
            $this->loader->getCurrentShop(),
            $this->loader->loadCurrentConfig(),
            $this->generateUniqueId(),
            $this->generateUniqueId(),
            $this->getPreCheckId($user)
        );
    }

    /**
     * Create with Transaction Infos
     *
     * @param string $transActionId
     * @param string $trxId
     * @param string $referenceId
     * @param array $user
     * @return RequestContext
     */
    public function createWithTransactionInfos($transActionId, $trxId, $referenceId, array $user)
    {
        return new RequestContext(
            $this->loader->getCurrentShop(),
            $this->loader->loadCurrentConfig(),
            $transActionId,
            $trxId,
            $this->getPreCheckId($user),
            $referenceId
        );
    }

    /**
     * Get Pre Check ID
     *
     * @param array $user
     * @return string
     */
    private function getPreCheckId(array $user)
    {
        return $this->componentManager->getDatabase()->fetchOne(
            'SELECT
                      uniqueId
                    FROM
                      bestit_payolution_userCheck
                    WHERE
                      userId = :userId
                    AND
                      paymentId = :paymentId',
            [
                ':userId' => $user['additional']['user']['id'],
                ':paymentId' => $user['additional']['user']['paymentID']
            ]
        );
    }
}