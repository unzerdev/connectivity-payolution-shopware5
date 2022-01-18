<?php

namespace PolPaymentPayolution\ComponentManager;

use Doctrine\DBAL\Connection;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use sAdmin;
use sArticles;
use sBasket;
use Shopware\Components\Model\ModelManager;
use sOrder;
use sSystem;
use Zend_Cache_Core;

/**
 * Interface ComponentManagerInterface
 *
 * @package PolPaymentPayolution\ComponentManager
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
interface ComponentManagerInterface
{
    /**
     * Get Admin Module
     *
     * @return sAdmin
     */
    public function getAdminModule();

    /**
     * Get Basket Module
     *
     * @return sBasket
     */
    public function getBasketModule();

    /**
     * Get System Module
     *
     * @return sSystem
     */
    public function getSystemModule();

    /**
     * Get Order Module
     *
     * @return sOrder
     */
    public function getOrderModule();

    /**
     * Get Article Module
     *
     * @return sArticles
     */
    public function getArticleModule();

    /**
     * Get Database
     *
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    public function getDatabase();

    /**
     * Get Dbal Connection
     *
     * @return Connection
     */
    public function getDbalConnection();

    /**
     * Get Cache Module
     *
     * @return Zend_Cache_Core
     */
    public function getCacheModule();

    /**
     * Get ModelManager
     *
     * @return ModelManager
     */
    public function getModelManager();
}
