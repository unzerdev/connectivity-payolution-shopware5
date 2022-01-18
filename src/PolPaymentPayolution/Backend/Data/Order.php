<?php

namespace PolPaymentPayolution\Backend\Data;


class Order
{
    public static function getOrderPositions($orderId, $type)
    {
        if ($type == 'refund') {
            $sql = 'SELECT
                  sod.id,
                  sod.name,
                  (soda.payolution_capture - soda.payolution_refund) as quantity,
                  ROUND(IF(so.net = 1, sod.price * (1+ (sod.tax_rate / 100)), sod.price) * (soda.payolution_capture - soda.payolution_refund),2) as amount,
                  0 as additionalId
                FROM
                  s_order_details sod
                INNER JOIN
                  s_order_details_attributes soda
                ON
                  sod.id = soda.detailID
                LEFT JOIN
                  s_order so
                ON
                  sod.orderID = so.id
                WHERE
                  sod.orderID = :orderId';
        } else {
            $sql = 'SELECT
                  sod.id,
                  sod.name,
                  (sod.quantity - soda.payolution_capture) as quantity,
                  ROUND(IF(so.net = 1, sod.price * (1+ (sod.tax_rate / 100)), sod.price) * (sod.quantity - soda.payolution_capture),2) as amount,
                  0 as additionalId
                FROM
                  s_order_details sod
                INNER JOIN
                  s_order_details_attributes soda
                ON
                  sod.id = soda.detailID
                LEFT JOIN
                  s_order so
                ON
                  sod.orderID = so.id
                WHERE
                  sod.orderID = :orderId';
        }

        $return = Shopware()->Db()->fetchAll($sql, array(':orderId' => $orderId));
        $shippingPosition = [
            'id' => 'invoice_shipping',
            'name' => Shopware()->Snippets()->getNamespace('backend/pol_payment_payolution/shipping')
                ->get(
                    'payolutionShippingName',
                    'Versandkosten',
                    true
                ),
            'additionalId' => 1,
            'quantity' => 0,
            'amount' => '0,00',
        ];
        $shipping = Shopware()->Db()->fetchRow(
            'SELECT
              soa.payolution_shipping,
              ROUND(so.invoice_shipping,2) as amount
            FROM
              s_order_attributes soa
            INNER JOIN
              s_order so
            ON
              soa.orderID = so.id
            WHERE
              orderID = :orderId',
            array(
                ':orderId' => $orderId
            )
        );

        if (($shipping['payolution_shipping'] == 0 && $type == 'capture')
            || ($shipping['payolution_shipping'] == 1 && $type == 'refund')
        ) {
            $shippingPosition['quantity'] = 1;
            $shippingPosition['amount'] = $shipping['amount'];
        }
        $return[] = $shippingPosition;

        $positionTotalAmount = Shopware()->Db()->fetchRow(
            'SELECT
              SUM(ROUND(IF(so.net = 1, sod.price * (1+ (sod.tax_rate / 100)), sod.price),2)  * sod.quantity) + so.invoice_shipping as amount,
              so.invoice_amount
            FROM
              s_order_details sod
            LEFT JOIN
              s_order so
            ON
              sod.orderID = so.id
            WHERE
              sod.orderID = :orderId',
            array(
                ':orderId' => $orderId
            )
        );

        $differenceAmount = round($positionTotalAmount['invoice_amount'] - $positionTotalAmount['amount'], 2);

        if ($differenceAmount > 0) {
            $difference = Shopware()->Db()->fetchOne(
                'SELECT
                  soa.payolution_difference
                FROM
                  s_order_attributes soa
                WHERE
                  orderID = :orderId',
                array(
                    ':orderId' => $orderId
                )
            );

            $differencePosition = [
                'id' => 'invoice_differnce',
                'name' => Shopware()->Snippets()->getNamespace('backend/pol_payment_payolution/differnce')
                    ->get(
                        'payolutionDiffernceName',
                        'restlicher Betrag',
                        true
                    ),
                'additionalId' => 2,
                'quantity' => 0,
                'amount' => '0,00',
            ];

            if (($difference['payolution_differnce'] == 0 && $type == 'capture')
                || ($difference['payolution_differnce'] == 1 && $type == 'refund')
            ) {
                $differencePosition['quantity'] = 1;
                $differencePosition['amount'] = $differenceAmount;
            }
            $return[] = $differencePosition;
        }

        return $return;
    }
}