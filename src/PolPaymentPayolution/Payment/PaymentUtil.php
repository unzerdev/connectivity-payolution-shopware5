<?php

namespace PolPaymentPayolution\Payment;

use PolPaymentPayolution\Enum\OrderPosition as OrderPositionEnum;
use PolPaymentPayolution\Payment\Factory\ShippingFactory;
use PolPaymentPayolution\Payment\Order\Amount;
use PolPaymentPayolution\Payment\Order\OrderPosition;
use PolPaymentPayolution\Payment\Order\OrderPositionIdentifier;
use PolPaymentPayolution\SnippetManager\SnippetManagerInterface;
use Shopware\Models\Order\Order;

/**
 * Class PaymentUtil
 *
 * @package PolPaymentPayolution\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PaymentUtil
{
    /**
     * @var SnippetManagerInterface
     */
    private $snippetManager;

    /**
     * @var ShippingFactory
     */
    private $shippingFactory;

    /**
     * PaymentUtil constructor.
     *
     * @param SnippetManagerInterface $snippetManager
     * @param ShippingFactory $shippingFactory
     */
    public function __construct(SnippetManagerInterface $snippetManager, ShippingFactory $shippingFactory)
    {
        $this->snippetManager = $snippetManager;
        $this->shippingFactory = $shippingFactory;
    }

    /**
     * Get Shipping By Order
     *
     * @param Order $order
     * @param string $type
     *
     * @return OrderPosition
     */
    public function getPayolutionShippingByOrder(Order $order, $type)
    {
        $shipping = $this->shippingFactory->createFromOrder($order, $type);

        $currency = $this->extractCurrencyFromOrder($order);
        $name = $this->snippetManager->getByName(
            'payolutionShippingName',
            'backend/pol_payment_payolution/shipping',
            'Versandkosten'
        );

        return new OrderPosition(
            new OrderPositionIdentifier(OrderPositionEnum::SHIPPING_ID, 1),
            new Amount($shipping->getAmount(), $currency),
            $name,
            $shipping->getQuantity()

        );
    }

    /**
     * Extract Currency from order
     *
     * @param Order $order
     *
     * @return string
     */
    public function extractCurrencyFromOrder(Order $order)
    {
        if (!$shop = $order->getShop()) {
            return '';
        }

        return $shop->getCurrency()->getCurrency();
    }
}