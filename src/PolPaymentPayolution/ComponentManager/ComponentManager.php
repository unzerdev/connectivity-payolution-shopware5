<?php

namespace PolPaymentPayolution\ComponentManager;

use Doctrine\DBAL\Connection;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use sAdmin;
use sArticles;
use sBasket;
use Shopware\Components\Model\ModelManager;
use Shopware_Components_Modules;
use sOrder;
use sSystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zend_Cache_Core;

/**
 * Class ComponentManager
 *
 * @package PolPaymentPayolution\ComponentManager
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ComponentManager implements ComponentManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private $shopwareContainer;

    /**
     * ComponentManager constructor.
     *
     * @param ContainerInterface $shopwareContainer
     */
    public function __construct(
        ContainerInterface $shopwareContainer
    ) {
        $this->shopwareContainer = $shopwareContainer;
    }

    /**
     * Get Admin Module
     *
     * @return sAdmin
     */
    public function getAdminModule()
    {
        return $this->modules()->Admin();
    }

    /**
     * Get Basket Module
     *
     * @return sBasket
     */
    public function getBasketModule()
    {
        return $this->modules()->Basket();
    }

    /**
     * Get System Module
     *
     * @return sSystem
     */
    public function getSystemModule()
    {
        return $this->modules()->System();
    }

    /**
     * Get Order Module
     *
     * @return sOrder
     */
    public function getOrderModule()
    {
        return $this->modules()->Order();
    }

    /**
     * Get Article Module
     *
     * @return sArticles
     */
    public function getArticleModule()
    {
        return $this->modules()->Articles();
    }

    /**
     * Get Database
     *
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    public function getDatabase()
    {
        return $this->shopwareContainer->get('db');
    }

    /**
     * Get Dbal Connection
     *
     * @return Connection
     */
    public function getDbalConnection()
    {
        return $this->shopwareContainer->get('dbal_connection');
    }

    /**
     * Get ModelManager
     *
     * @return ModelManager
     */
    public function getModelManager()
    {
        return $this->shopwareContainer->get('models');
    }

    /**
     * Get Cache Module
     *
     * @return Zend_Cache_Core
     */
    public function getCacheModule()
    {
        return $this->shopwareContainer->get('cache');
    }

    /**
     * Get Modules
     *
     * @return Shopware_Components_Modules
     */
    private function modules()
    {
        /** @var Shopware_Components_Modules $modules */
        $modules = $this->shopwareContainer->get('modules');

        return $modules;
    }
}
