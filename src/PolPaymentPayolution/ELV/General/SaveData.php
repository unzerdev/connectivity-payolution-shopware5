<?php
namespace PolPaymentPayolution\ELV\General;

use \Zend_Db_Adapter_Abstract as Zend;

class SaveData
{
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
     * set installment for payolution
     *
     * @param $data
     * @param $userId
     */
    public function saveData($data, $userId)
    {
        $this->db->query(
            'REPLACE INTO
              bestit_payolution_elv
            (`userId`,`accountHolder`,`accountBic`,`accountIban`)
            VALUES
              (:userId,
              :accountHolder,
              :accountBic,
              :accountIban)',
            array(
                ':userId' => $userId,
                ':accountHolder' => $data['elvHolder'],
                ':accountBic' => $data['elvBic'],
                ':accountIban' => $data['elvIban'],
            )
        );
    }
}
