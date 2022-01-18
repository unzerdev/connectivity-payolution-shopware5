<?php

namespace PolPaymentPayolution\Payment\Order\Factory;

use PolPaymentPayolution\Enum\OrderPosition as OrderPositionEnum;
use PolPaymentPayolution\Enum\PaymentType;
use PolPaymentPayolution\Payment\Order\Amount;
use PolPaymentPayolution\Payment\Order\OrderPosition;
use PolPaymentPayolution\Payment\Order\OrderPositionIdentifier;
use PolPaymentPayolution\Payment\PaymentUtil;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

/**
 * Class PositionFactory
 *
 * @package PolPaymentPayolution\Payment\Order\Factory
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PositionFactory
{
    /**
     * @var PaymentUtil
     */
    private $paymentUtil;

    /**
     * @var WorkflowRepository
     */
    private $workFlowRepository;

    /**
     * @var CapturePositionFactory
     */
    private $captureFactory;

    /**
     * @var RefundPositionFactory
     */
    private $refundFactory;

    /**
     * PositionFactory constructor.
     *
     * @param PaymentUtil $paymentUtil
     * @param WorkflowRepository $workFlowRepository
     * @param CapturePositionFactory $captureFactory
     * @param RefundPositionFactory $refundFactory
     */
    public function __construct(
        PaymentUtil $paymentUtil,
        WorkflowRepository $workFlowRepository,
        CapturePositionFactory $captureFactory,
        RefundPositionFactory $refundFactory
    ) {
        $this->paymentUtil = $paymentUtil;
        $this->workFlowRepository = $workFlowRepository;
        $this->captureFactory = $captureFactory;
        $this->refundFactory = $refundFactory;
    }

    /**
     * Create Positions
     *
     * @param Order $order
     * @param string $type
     *
     * @return array
     */
    public function createPosition(Order $order, $type)
    {
        $currency = $this->paymentUtil->extractCurrencyFromOrder($order);

        $elements = $this->workFlowRepository->getAllElementsForOrder($order->getId());

        $positions = array_map(function (Detail $detail) use ($currency, $type, $elements) {

            $element = null;

            if (isset($elements[$detail->getId()])) {
                $element = $elements[$detail->getId()];
            }

            if ($element && $type === PaymentType::CAPTURE) {
                return $this->captureFactory->createFromElement($element, $currency);
            } elseif ($element && $type === PaymentType::REFUND) {
                return $this->refundFactory->createFromElement($element, $currency);
            }

            return new OrderPosition(
                new OrderPositionIdentifier((string) $detail->getId()),
                new Amount(
                    $detail->getPrice() * $detail->getQuantity(),
                    $currency
                ),
                $detail->getArticleName(),
                $detail->getQuantity()
            );
        }, $order->getDetails()->toArray());


        if ($shippingPosition = $this->createShippingPosition($elements, $order, $type)) {
            $positions[] = $shippingPosition;
        }

        $positions = array_filter($positions);

        $this->persistPosition($positions, $order);

        $positions = array_merge($positions, $this->createAdditionalPositions($elements, $order, $type));

        return $positions;
    }

    /**
     * Create Shipping Position
     *
     * @param array $elements
     * @param Order $order
     * @param string $type
     *
     * @return null|OrderPosition
     */
    private function createShippingPosition(array $elements, Order $order, $type)
    {
        $currency = $this->paymentUtil->extractCurrencyFromOrder($order);

        $position = null;
        $shippingElement = null;
        if (isset($elements[OrderPositionEnum::SHIPPING_ID])) {
            $shippingElement = $elements[OrderPositionEnum::SHIPPING_ID];
        }

        if ($shippingElement && $type === PaymentType::CAPTURE) {
            $position = $this->captureFactory->createFromElement($shippingElement, $currency);
        } elseif($shippingElement && $type === PaymentType::REFUND) {
            $position = $this->refundFactory->createFromElement($shippingElement, $currency);
        } else {
            $position = $this->paymentUtil->getPayolutionShippingByOrder($order, $type);
        }

        return $position;
    }

    /**
     * Create Additional Positions
     *
     * @param array $elements
     * @param Order $order
     * @param string $type
     *
     * @return array
     */
    private function createAdditionalPositions(array $elements, Order $order, $type)
    {
        $positions = [];

        if ($type === PaymentType::REFUND) {

            $currency = $this->paymentUtil->extractCurrencyFromOrder($order);

            $amount = 0;
            $name = null;
            /**
             * @var WorkflowElement $element
             */
            foreach ($elements as $element) {
                if ($element->getIdentifier() === OrderPositionEnum::DIFFERENCE_PRINCE && !$element->isRefunded()) {
                    $amount += $element->getAmount();
                    $name = $element->getName();

                    continue;
                }

                if ($name && $element->getIdentifier() === OrderPositionEnum::ORDER_REFUND_IDENTIFIER) {
                    $amount -= $element->getAmount();
                }
            }

            if ($amount > 0) {
                $positions [] = $this->refundFactory->createAbsoluteElement($amount, $name, $currency);
            }
        }

        return $positions;
    }

    /**
     * Persist Positions
     *
     * @param array $positions
     * @param Order $order
     *
     * @return void
     */
    private function persistPosition(array $positions, Order $order)
    {
        $this->workFlowRepository->createFromPositions($positions, $order);
    }
}