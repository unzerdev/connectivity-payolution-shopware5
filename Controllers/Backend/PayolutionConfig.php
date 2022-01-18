<?php
/**
 * Class Shopware_Controllers_Backend_PayolutionConfig
 *
 * Provides function for shop/currencies/config actions
 */
class Shopware_Controllers_Backend_PayolutionConfig extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $dbAdapter;

    /**
     * Default index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->View()->loadTemplate('backend/payolution_config/app.js');
    }

    /**
     * Currencies action
     *
     * @return void
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function getCurrenciesAction()
    {
        $sql = 'SELECT id, name FROM s_core_currencies';
        $currencies = $this->getDbAdapter()->fetchAll($sql);

        $this->View()->assign(
            array(
                'success' => true,
                'data' => $currencies,
                'total' => count($currencies),
            )
        );
    }

    /**
     * Shop action
     *
     * @return void
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function getShopsAction()
    {
        $sql = 'SELECT id, name FROM s_core_shops';
        $shops = $this->getDbAdapter()->fetchAll($sql);

        $this->View()->assign(
            array(
                'success' => true,
                'data' => $shops,
                'total' => count($shops),
            )
        );
    }

    /**
     * Get config
     *
     * @return void
     */
    public function getConfigAction()
    {
        $start = $this->Request()->get('start') === null ? 0 : (int)$this->Request()->get('start');
        $limit = $this->Request()->get('limit') === null ? 25 : (int)$this->Request()->get('limit');

        $sql = <<<sql
SELECT *
FROM bestit_payolution_config bpc
       LEFT JOIN bestit_payolution_config_order bpco ON bpc.name = bpco.name
ORDER BY bpc.shopId ASC,
         bpc.currencyId ASC,
         bpco.order ASC
LIMIT :limit OFFSET :offset
sql;

        $stmt = $this->getDbAdapter()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \Zend_Db::PARAM_INT);
        $stmt->bindValue(':offset', $start, \Zend_Db::PARAM_INT);
        $stmt->execute();
        $config = $stmt->fetchAll();

        $total = $this->getDbAdapter()->fetchOne('SELECT count(*) FROM bestit_payolution_config');

        $this->View()->assign(
            [
                'success' => true,
                'data' => $config,
                'total' => $total,
            ]
        );
    }

    /**
     * Update config
     *
     * @return void
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function updateConfigAction()
    {
        $shopId = $this->Request()->shopId;
        $currencyId = $this->Request()->currencyId;
        $name = $this->Request()->name;
        $value = $this->Request()->value;

        $sql = 'UPDATE bestit_payolution_config
                SET value = :value
                WHERE shopId = :shopId
                AND currencyId = :currencyId
                AND name = :name';

        $params = [
            ':value' => trim($value),
            ':shopId' => $shopId,
            ':currencyId' => $currencyId,
            ':name' => $name,
        ];

        $this->getDbAdapter()->query($sql, $params);
    }

    /**
     * Get DbAdapter
     *
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private function getDbAdapter()
    {
        if (!$this->dbAdapter) {
            $this->dbAdapter = $this->container->get('db');
        }

        return $this->dbAdapter;
    }
}
