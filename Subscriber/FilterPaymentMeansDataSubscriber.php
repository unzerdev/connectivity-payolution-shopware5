<?php

namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;

/**
 * Subscriber when user filters payments.
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class FilterPaymentMeansDataSubscriber implements SubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Admin_GetPaymentMeans_DataFilter' => 'filterPaymentMeans'
        ];
    }

    /**
     * Event function that enriches the payment array in the response.
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return array
     */
    public function filterPaymentMeans(Enlight_Event_EventArgs $args)
    {
        $payments = $args->getReturn();

        foreach ($payments as $index => $payment) {
            switch ($payment['name']) {
                case 'payolution_invoice_b2c':
                    $payments[$index]['payolutionB2c']['checkout'] = true;
                    break;
                case 'payolution_installment':
                    $payments[$index]['payolutionInstallment']['checkout'] = true;
                    break;
                case 'payolution_invoice_b2b':
                    $payments[$index]['payolutionB2b']['checkout'] = true;
                    break;
                case 'payolution_elv':
                    $payments[$index]['payolutionElv']['checkout'] = true;
                    break;
            }
        }

        $args->setReturn($payments);

        return $payments;
    }
}
