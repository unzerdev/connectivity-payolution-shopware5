<?php

namespace PolPaymentPayolution\Fetcher;

use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;

/**
 * Class OrderDataFetcher
 *
 * @package PolPaymentPayolution\Fetcher
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class OrderDataFetcher
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * OrderDataFetcher constructor.
     *
     * @param ComponentManagerInterface $componentManager
     */
    public function __construct(ComponentManagerInterface $componentManager)
    {
        $this->componentManager = $componentManager;
    }

    /**
     * Fetch Order Data Array
     *
     * @param int $orderId
     *
     * @return array
     */
    public function fetchOrderData($orderId)
    {
        $qb = $this->componentManager->getDbalConnection()->createQueryBuilder();

        $qb->from('s_order', 'so')
            ->leftJoin('so', 's_premium_dispatch', 'spd', 'so.dispatchID = spd.id')
            ->innerJoin('so', 's_user', 'su', 'su.id = so.userID')
            ->innerJoin('so', 's_order_attributes', 'soa', 'soa.orderID = so.id')
            ->innerJoin('so', 's_core_paymentmeans', 'scp', 'so.paymentID = scp.id')
            ->select($this->getSelect())
            ->where($qb->expr()->eq('so.id', ':orderId'))
            ->setParameter('orderId', $orderId);

        $result = $qb->execute()->fetchAll();

        if (!isset($result[0])) {
            return [];
        }
        $result = $result[0];

        return [
            'dispatchName' => $result['dispatchName'],
            'trackingcode' => $result['trackingcode'],
            'taxAmount' => $result['taxAmount'],
            'ordernumber' => $result['ordernumber'],
            'customernumber' => $result['customernumber'],
            'currency' => $result['currency'],
            'amount' => $result['amount'],
            'invoiceId' => $result['invoiceId'],
            'referenceId' => $result['referenceId'],
            'payolutionMode' => strtoupper($result['payolutionMode']),
            'taxRate' => $result['tax'],
            'capturedAmount' => $result['payolution_capture'],
            'refundedAmount' => $result['payolution_refund'],
        ];
    }

    /**
     * Get Select
     *
     * @return string
     */
    private function getSelect()
    {
        return
            'so.dispatchID,' .
            'so.trackingcode,' .
            'so.ordernumber,' .
            'so.currency,' .
            'so.invoice_amount as amount,' .
            'ROUND(so.invoice_amount - so.invoice_amount_net,2) AS taxAmount,' .
            'ROUND(so.invoice_amount / so.invoice_amount_net,2) as tax,' .
            'spd.name as dispatchName,' .
            'su.customernumber,' .
            'soa.payolution_unique_id as referenceId,' .
            'soa.payolution_invoice_id as invoiceId,' .
            'soa.payolution_capture,' .
            'soa.payolution_refund,' .
            'scp.name as payolutionMode';
    }
}
