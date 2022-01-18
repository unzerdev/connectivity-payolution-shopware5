<?php

namespace PolPaymentPayolution\RiskManagement;

use \Zend_Db_Adapter_Abstract as Zend;

/**
 * Class CheckPersonal
 *
 * @package PolPaymentPayolution\RiskManagement
 * @deprecated Use future class instead.
 */
class CheckPersonal
{
    /**
     * @var Zend
     */
    private $db;

    /**
     * class constructor
     *
     * @param Zend $db
     */
    public function __construct(Zend $db)
    {
        $this->db = $db;
    }

    /**
     * get user birthday
     *
     * @param $userId
     * @return bool
     */
    public function getBirthday($userId)
    {
        $birthday = $this->db->fetchOne(
            'SELECT
                birthday
              FROM
                s_user
              WHERE
                id = :userId',
            array(
                ':userId' => $userId
            )
        );

        return $birthday;
    }

    /**
     * set birthday for payolution
     *
     * @param $birthday
     * @param $userId
     */
    public function setBirthday($birthday, $userId)
    {
        $this->db->query(
            'UPDATE
              s_user
            SET
              birthday = :birthday
            WHERE
              id = :userId',
            array(
                ':birthday' => $birthday,
                ':userId' => $userId,
            )
        );
    }
}
