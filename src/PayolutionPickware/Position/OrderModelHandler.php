<?php

namespace PayolutionPickware\Position;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Payolution\Workflow\RefundInvoker;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\Enum\OrderPosition;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowHistoryRepository;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;
use Shopware\Models\Attribute\OrderDetail;
use Shopware\Models\Order\Order;

/**
 * Class PositionAttributeHandler
 *
 * @package PayolutionPickware\Position
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class OrderModelHandler
{
    /**
     * Cancellation Key
     *
     * @var string
     */
    const VISION_CANCEL_FIELD = 'viisonCanceledQuantity';

    /**
     * Shipping Key
     *
     * @var string
     */
    const INVOICE_SHIPPING_FIELD = 'invoiceShipping';

    /**
     * @var WorkflowRepository
     */
    private $workFlowRepository;

    /**
     * @var RefundInvoker
     */
    private $refundInvoker;

    /**
     * @var PluginConfig
     */
    private $config;

    /**
     * @var WorkflowHistoryRepository
     */
    private $workFlowHistoryRepo;

    /**
     * OrderModelHandler constructor.
     *
     * @param WorkflowRepository $workFlowRepository
     * @param RefundInvoker $refundInvoker
     * @param PluginConfig $config
     * @param WorkflowHistoryRepository $workFlowHistoryRepo
     */
    public function __construct(
        WorkflowRepository $workFlowRepository,
        RefundInvoker $refundInvoker,
        PluginConfig $config,
        WorkflowHistoryRepository $workFlowHistoryRepo
    ) {
        $this->workFlowRepository = $workFlowRepository;
        $this->refundInvoker = $refundInvoker;
        $this->config = $config;
        $this->workFlowHistoryRepo = $workFlowHistoryRepo;
    }

    /**
     * Process Order Attribute Pre Update LifeCycle Event
     *
     * @param PreUpdateEventArgs $event
     *
     * @return void
     */
    public function processAttributeUpdate(PreUpdateEventArgs $event)
    {
        if (!$event->hasChangedField(self::VISION_CANCEL_FIELD) || !$this->config->isAutomaticRefundCancellationPositions()) {
            return;
        }

        $newValue = $event->getNewValue(self::VISION_CANCEL_FIELD);
        $oldValue = $event->getOldValue(self::VISION_CANCEL_FIELD);

        if ($oldValue === $newValue) {
            return;
        }

        if ($oldValue !== 0) {
            $newValue -= $oldValue;
        }

        /**
         * @var OrderDetail $orderDetail
         */
        $orderDetail = $event->getEntity();
        $order = $orderDetail->getOrderDetail()->getOrder();
        if (!$element = $this->workFlowRepository->getElementByIdentifier($orderDetail->getOrderDetailId(), $order->getId())) {
            return;
        }

        if (!$element->isCaptured() || $newValue > $element->getCapturedQuantity()) {
            $this->createErrorLogEntry(
                sprintf(
                    'Order Position %s not captured',
                    $element->getName()
                ),
                $element,
                $newValue
            );

            return;
        }

        if ($element->isRefunded()) {
            $this->createErrorLogEntry(
                sprintf(
                    'Order Position %s already refunded',
                    $element->getName()
                ),
                $element,
                $newValue
            );

            return;
        }

        // Handle Position Refund
        $this->refundInvoker->invokeRefundElement($element, $newValue);
    }

    /**
     * Process Order Pre Update LifeCycle Event
     *
     * @param PreUpdateEventArgs $event
     *
     * @return void
     */
    public function processOrderUpdate(PreUpdateEventArgs $event)
    {
        if (!$this->config->isAutomaticRefundCancellationPositions()
            || !$event->hasChangedField(self::INVOICE_SHIPPING_FIELD)
        ) {
            return;
        }
        $newValue = $event->getNewValue(self::INVOICE_SHIPPING_FIELD);

        if ($newValue !== 0) {
            return;
        }

        if ($event->getOldValue(self::INVOICE_SHIPPING_FIELD) === (float) $newValue) {
            return;
        }

        /**
         * @var Order $order
         */
        $order = $event->getEntity();
        if (!$element = $this->workFlowRepository->getElementByIdentifier(
            OrderPosition::SHIPPING_ID,
            $order->getId()
        )){
            return;
        }

        if ($element->isRefunded()) {
            $this->createErrorLogEntry(
                sprintf(
                    'Order Position %s already refunded',
                    $element->getName()
                ),
                $element,
                $newValue
            );

            return;
        }

        // Handle Shipping Refund
        $this->refundInvoker->invokeRefundElement($element, 1);
    }

    /**
     * Create Error Log Entity
     *
     * @param string $message
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return void
     */
    private function createErrorLogEntry($message, WorkflowElement $element, $quantity)
    {
        $amount = ($element->getAmount() / $element->getQuantity()) * $quantity;
        if ($amount <= 0) {
            $amount = $element->getAmount();
        }

        $this->workFlowHistoryRepo->createRefundErrorEntity($message, $amount, $quantity, $element);

    }
}