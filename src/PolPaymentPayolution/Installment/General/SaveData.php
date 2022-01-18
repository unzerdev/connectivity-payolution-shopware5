<?php
namespace PolPaymentPayolution\Installment\General;

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
        $this->db->query('
            UPDATE
              bestit_payolution_installment
            SET
              amount = :amount,
              duration = :duration,
              accountHolder = :holder,
              accountBic = :bic,
              accountIban = :iban
            WHERE
              userId = :userId',
            array(
                ':amount' => $data['amount'],
                ':duration' => $data['duration'],
                ':holder' => $data['holder'],
                ':bic' => $data['bic'],
                ':iban' => $data['iban'],
                ':userId' => $userId,
            )
        );
    }
}
