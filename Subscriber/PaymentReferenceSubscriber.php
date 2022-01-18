<?php
namespace PolPaymentPayolution\Subscriber;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Enlight\Event\SubscriberInterface;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Enlight_Event_EventArgs;
use Psr\Log\LoggerInterface;
use Zend_Db_Adapter_Exception;

/**
 * Save payment reference on order creation
 *
 * @package PolPaymentPayolution\Subscriber
 */
class PaymentReferenceSubscriber implements SubscriberInterface
{
    /**
     * The logger
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The database connection
     *
     * @var Connection
     */
    private $dbAdapter;

    /**
     * PaymentReferenceSubscriber constructor.
     *
     * @param LoggerInterface $logger
     * @param Enlight_Components_Db_Adapter_Pdo_Mysql $dbAdapter
     */
    public function __construct(LoggerInterface $logger, Enlight_Components_Db_Adapter_Pdo_Mysql $dbAdapter)
    {
        $this->logger = $logger;
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Order_SaveOrder_ProcessDetails' => 'saveReference'
        ];
    }

    /**
     * Save payment reference on order save
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return void
     *
     * @throws Zend_Db_Adapter_Exception
     * @throws DBALException
     */
    public function saveReference(Enlight_Event_EventArgs $args)
    {
        $details = $args->get('details');

        $paymentReferenceId = $this->dbAdapter->fetchOne(
            'SELECT
              sua.payolution_payment_reference_id_temp
            FROM
              s_user_attributes sua
            INNER JOIN
              s_order so ON so.userID = sua.userID
            INNER JOIN
              s_order_details sod ON sod.orderID = so.id
            WHERE
              sod.id = :detailsId LIMIT 1',
            [
                ':detailsId' => $details[0]['orderDetailId'],
            ]
        );

        $orderId = $this->dbAdapter->fetchOne(
            'SELECT
              so.id
            FROM
              s_order so
            INNER JOIN
              s_order_details sod
            ON
              sod.orderID = so.id
            WHERE
              sod.id = :detailsId
            LIMIT 1',
            [
                ':detailsId' => $details[0]['orderDetailId'],
            ]
        );

        $this->logger->info(
            'Logging saving Payment Reference',
            [
                'paymentReference' => $paymentReferenceId,
                'userId' => $details[0]['userID'],
                'detailsId' => $details[0]['orderDetailId'],
                'orderId' => $orderId,
            ]
        );

        $this->dbAdapter->query(
            'UPDATE
              s_order_attributes
            SET
              payolution_payment_reference_id = :paymentReferenceId
            WHERE
              orderID = :orderId',
            [
                ':paymentReferenceId' => $paymentReferenceId,
                ':orderId' => $orderId,
            ]
        );
    }
}
