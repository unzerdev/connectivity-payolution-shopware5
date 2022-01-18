<?php

namespace PolPaymentPayolution\Payment;

use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Payment\Order\PositionProvider;
use Shopware\Models\Attribute\Order as OrderAttribute;
use Shopware\Models\Order\Order;

/**
 * Class PaymentInvoker
 *
 * @package PolPaymentPayolution\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PaymentInvoker
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var PositionProvider
     */
    private $positionProvider;

    /**
     * PaymentInvoker constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param PositionProvider $positionProvider
     */
    public function __construct(ComponentManagerInterface $componentManager, PositionProvider $positionProvider)
    {
        $this->componentManager = $componentManager;
        $this->positionProvider = $positionProvider;
    }

    /**
     * Invoke After Success Handler
     *
     * @param int $orderNumber
     *
     * @return void
     */
    public function invokeSuccessfulPayment($orderNumber)
    {
        $modelManager = $this->componentManager->getModelManager();

        $order = $modelManager->getRepository(Order::class)->findOneBy([
            'number' => $orderNumber
        ]);

        if (!$order) {
            return;
        }

        /**
         * @var OrderAttribute $attributes
         */
        $attributes = $order->getAttribute();
        $attributes->setPayolutionShipping($order->getInvoiceShipping());

        $modelManager->flush($attributes);

        $this->positionProvider->getCaptureCollection($order->getId());
    }
}