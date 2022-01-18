<?php

namespace PolPaymentPayolution\Payment\Capture;

use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Enum\OrderPosition;
use PolPaymentPayolution\Payment\Order\Amount;
use PolPaymentPayolution\Payment\PaymentUtil;
use PolPaymentPayolution\SnippetManager\SnippetManagerInterface;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;
use Shopware\Models\Order\Order;

/**
 * Class WorkflowAmountProvider
 *
 * @package PolPaymentPayolution\Payment\Capture
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowAmountProvider
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var WorkflowRepository
     */
    private $workflowRepository;

    /**
     * @var PaymentUtil
     */
    private $paymentUtil;

    /**
     * @var SnippetManagerInterface
     */
    private $snippetManager;

    /**
     * WorkflowAmountProvider constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param WorkflowRepository $workflowRepository
     * @param PaymentUtil $paymentUtil
     * @param SnippetManagerInterface $snippetManager
     */
    public function __construct(
        ComponentManagerInterface $componentManager,
        WorkflowRepository $workflowRepository,
        PaymentUtil $paymentUtil,
        SnippetManagerInterface $snippetManager
    ) {
        $this->componentManager = $componentManager;
        $this->workflowRepository = $workflowRepository;
        $this->paymentUtil = $paymentUtil;
        $this->snippetManager = $snippetManager;
    }

    /**
     * Get Workflow Amount
     *
     * @param int $orderId
     *
     * @return WorkflowAmount
     */
    public function getWorkflowAmount($orderId)
    {
        if (!$order = $this->componentManager->getModelManager()->find(Order::class, $orderId)) {
            return null;
        }

        $currency = $this->paymentUtil->extractCurrencyFromOrder($order);

        $captureSnippet = $this->snippetManager->getByName(
            'payolutionPossibleCaptureAmount',
            'backend/pol_payment_payolution/backend/capture',
            'Capture - maximaler Capturebetrag: %s'
        );

        $refundSnippet = $this->snippetManager->getByName(
            'payolutionPossibleRefundAmount',
            'backend/pol_payment_payolution/backend/refund',
            'Refund - maximaler Refundbetrag: %s'
        );

        $elements = $this->workflowRepository->getAllElementsForOrder($orderId);

        return new WorkflowAmount(
            $this->getCaptureAmount($currency, $elements),
            $this->getRefundAmount($currency, $elements),
            new Amount($order->getInvoiceAmount(), $currency),
            $captureSnippet,
            $refundSnippet
        );
    }

    /**
     * Get Capture Amount
     *
     * @param string $currency
     * @param array $elements
     *
     * @return Amount
     */
    private function getCaptureAmount($currency, array $elements)
    {
        $amount = 0;

        /**
         * @var WorkflowElement $element
         */
        foreach ($elements as $element) {
            if ($element->getIdentifier() === OrderPosition::DIFFERENCE_PRINCE) {
                $amount -= $element->getAmount();

                continue;
            }

            if ($element->isCaptured()) {
                continue;
            }
            $captureQuantity = $element->getQuantity() - $element->getCapturedQuantity();
            $captureAmount = $captureQuantity === $element->getQuantity()
                ? $element->getAmount()
                : ($element->getAmount() / $element->getQuantity()) * $captureQuantity;

            $amount += round($captureAmount, 2);
        }

        if ($amount < 0) {
            $amount = 0;
        }

        return new Amount($amount, $currency);
    }

    /**
     * Get Refund Amount
     *
     * @param string $currency
     * @param array $elements
     *
     * @return Amount
     */
    private function getRefundAmount($currency, array $elements)
    {
        $amount = 0;

        /**
         * @var WorkflowElement $element
         */
        foreach ($elements as $element) {
            if ($element->getIdentifier() === OrderPosition::ORDER_REFUND_IDENTIFIER) {
                $amount -= $element->getAmount();

                continue;
            }

            if ($element->isRefunded() || $element->getCapturedQuantity() === 0) {
                continue;
            }
            $refundQuantity = $element->getCapturedQuantity() - $element->getRefundedQuantity();

            $refundAmount = $refundQuantity === $element->getQuantity()
                ? $element->getAmount()
                : ($element->getAmount() / $element->getQuantity()) * $refundQuantity;

            $amount += round($refundAmount, 2);
        }

        if ($amount < 0) {
            $amount = 0;
        }

        return new Amount($amount, $currency);
    }
}