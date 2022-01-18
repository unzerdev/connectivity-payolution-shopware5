<?php

namespace PolPaymentPayolution\Backend\Payment;

use Enlight_Components_Db_Adapter_Pdo_Mysql;

/**
 * Class Log
 *
 * @package PolPaymentPayolution\Backend\Payment
 */
class Log
{
    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * Log constructor.
     *
     * @param Enlight_Components_Db_Adapter_Pdo_Mysql $db
     */
    public function __construct(Enlight_Components_Db_Adapter_Pdo_Mysql $db)
    {
        $this->db = $db;
    }

    /**
     * Returns log data by given orderId
     *
     * @param integer $orderId
     *
     * @return array
     */
    public function getLogData($orderId)
    {
        return $this->db->fetchAll('
            SELECT
              *
            FROM
              bestit_payolution_cr_log
            WHERE
              orderId = :orderId
        ', [
            ':orderId' => $orderId
        ]);
    }

    /**
     * Sets log data.
     *
     * @param array $data
     *
     * @return array
     */
    public function setLogData(array $data)
    {
        foreach ($data as $value) {
            $this->db->query('
                INSERT INTO
                  bestit_payolution_cr_log
                  (
                    `orderId`,
                    `date`,
                    `articlename`,
                    `quantity`,
                    `amount`,
                    `type`
                  )
                  VALUES
                  (
                    :orderId,
                    NOW(),
                    :articleName,
                    :quantity,
                    :amount,
                    :type
                  )
            ', [
                ':orderId' => $value['orderId'],
                ':articleName' => $value['articleName'],
                ':quantity' => $value['quantity'],
                ':amount' => $value['amount'],
                ':type' => $value['type'],
            ]);
        }

        return $data;
    }
}
