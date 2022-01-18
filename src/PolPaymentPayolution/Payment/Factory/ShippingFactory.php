<?php

namespace PolPaymentPayolution\Payment\Factory;

use PolPaymentPayolution\Enum\OrderPosition;
use PolPaymentPayolution\Enum\PaymentType;
use PolPaymentPayolution\Payment\PayolutionShipping;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;
use Shopware\Models\Attribute\Order as OrderAttribute;
use Shopware\Models\Order\Order;

/**
 * Class ShippingFactory
 *
 * @package PolPaymentPayolution\Payment\Factory
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ShippingFactory
{
    /**
     * @var WorkflowRepository
     */
    private $repository;

    /**
     * ShippingFactory constructor.
     *
     * @param WorkflowRepository $repository
     */
    public function __construct(WorkflowRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create From Order
     *
     * @param Order $order
     * @param string $type
     *
     * @return PayolutionShipping
     */
    public function createFromOrder(Order $order, $type)
    {
        $shippingPosition = $this
            ->repository
            ->getElementByIdentifier(OrderPosition::SHIPPING_ID, $order->getId());

        $shipmentAmount = $this->getShippingAmount($order);

        if (!$shippingPosition && $type === PaymentType::CAPTURE) {
            return new PayolutionShipping($shipmentAmount, 1);
        } elseif ($shippingPosition && $shippingPosition->isCaptured() && PaymentType::REFUND) {
            return new PayolutionShipping($shipmentAmount, 1);
        }

        return new PayolutionShipping(0.00, 0);
    }

    /**
     * Get ShippingAmount
     *
     * @param Order $order
     *
     * @return float
     */
    private function getShippingAmount(Order $order)
    {
        /**
         * @var OrderAttribute
         */
        $orderAttributes = $order->getAttribute();

        $orderAmount = $order->getInvoiceShipping();

        if ($orderAmount === (float) 0) {
            $shippingAmount = $orderAttributes->getPayolutionShipping();
        } else {
            $shippingAmount = $orderAmount;
        }

        return (float) $shippingAmount;
    }
}