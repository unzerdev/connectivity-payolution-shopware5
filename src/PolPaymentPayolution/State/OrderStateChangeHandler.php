<?php

namespace PolPaymentPayolution\State;

use Enlight_Controller_Front;
use Payolution\Workflow\CaptureInvoker;
use Payolution\Workflow\RefundInvoker;
use Payolution\Workflow\ReversalInvoker;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Config\PluginConfig;
use Psr\Log\LoggerInterface;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;
use Shopware\Models\Order\Order;

/**
 * Class OrderStateChangeHandler
 *
 * @package PolPaymentPayolution\State
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class OrderStateChangeHandler
{
    /**
     * Refund Mode
     *
     * @var int
     */
    const WHOLE_REFUND_MODE = 1;

    /**
     * Capture Mode
     *
     * @var int
     */
    const WHOLE_CAPTURE_MODE = 2;

    /**
     * Reversal Mode
     *
     * @var int
     */
    const WHOLE_REVERSAL_MODE = 3;

    /**
     * Skip Mode
     *
     * @var int
     */
    const MODE_SKIP = 0;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CaptureInvoker
     */
    private $captureInvoker;

    /**
     * @var RefundInvoker
     */
    private $refundInvoker;

    /**
     * @var ReversalInvoker
     */
    private $reversalInvoker;

    /**
     * @var WorkflowRepository
     */
    private $workFlowRepository;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * OrderStateChangeHandler constructor.
     *
     * @param LoggerInterface $logger
     * @param CaptureInvoker $captureInvoker
     * @param RefundInvoker $refundInvoker
     * @param ReversalInvoker $reversalInvoker
     * @param WorkflowRepository $workFlowRepository
     * @param PluginConfig $pluginConfig
     */
    public function __construct(
        LoggerInterface $logger,
        CaptureInvoker $captureInvoker,
        RefundInvoker $refundInvoker,
        ReversalInvoker $reversalInvoker,
        WorkflowRepository $workFlowRepository,
        PluginConfig $pluginConfig
    ) {
        $this->logger = $logger;
        $this->captureInvoker = $captureInvoker;
        $this->refundInvoker = $refundInvoker;
        $this->reversalInvoker = $reversalInvoker;
        $this->workFlowRepository = $workFlowRepository;
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * Get Order State Change by History and current state
     *
     * @param int $currentStatusId
     * @param int $newOrderStateId
     *
     * @return int
     */
    public function getOrderStateChangeMode($currentStatusId = null, $newOrderStateId = null)
    {
        $mode = self::MODE_SKIP;

        if ($currentStatusId !== $newOrderStateId) {
            $mode = $this->getChangeMode($newOrderStateId);
        }

        $this->logger->debug(
            'Get Order State Change Mode',
            [
                'currentStatus' => $newOrderStateId,
                'newStatus' => $currentStatusId,
                'mode' => $mode
            ]
        );

        return $mode;
    }

    /**
     * Get Change Mode
     *
     * @param int|null $orderState
     *
     * @return int
     */
    public function getChangeMode($orderState = null)
    {
        $captureState = $this->pluginConfig->getCaptureOrderState();
        $refundState = $this->pluginConfig->getRefundOrderState();

        $this->logger->debug('Get changemode', ['captureState' => $captureState, 'refundState' => $refundState]);

        switch ($orderState) {
            case $captureState:
                $mode = self::WHOLE_CAPTURE_MODE;
                break;
            case $refundState:
                $mode = self::WHOLE_REFUND_MODE;
                break;
            default:
                $mode = self::MODE_SKIP;
                break;
        }

        return $mode;
    }

    /**
     * Get Cancel Mode
     *
     * @param int
     *
     * @return int
     */
    public function getCancelMode($orderId)
    {
        $captured = false;
        $mode = self::WHOLE_REVERSAL_MODE;

        $elements = $this->workFlowRepository->getAllElementsForOrder($orderId);

        foreach ($elements as $element) {
            if ($element->isCaptured()) {
                $captured = true;

                break;
            }
        }

        if ($captured) {
            $mode = self::WHOLE_REFUND_MODE;

        }

        return $mode;
    }

    /**
     * Invoke Automatic Order Process
     *
     * @param Order $order
     * @param int $mode
     *
     * @return bool
     */
    public function invokeAutomaticOrderProcess(Order $order, $mode)
    {
        $this->logger->info(
            sprintf('Initiate Order State Change with mode %s for order %s', $mode, $order->getNumber())
        );

        $resultArray = [];
        $processed = false;
        switch ($mode) {
            case self::WHOLE_CAPTURE_MODE:
                $resultArray = $this->captureInvoker->invokeCaptureWholeOrder($order->getId());
                $processed = true;
                break;
            case self::WHOLE_REFUND_MODE:
                $resultArray = $this->refundInvoker->invokeRefundWholeOrder($order->getId());
                $processed = true;
                break;
            case self::WHOLE_REVERSAL_MODE:
                $resultArray = $this->reversalInvoker->invokeReversal($order->getId());
                $processed = true;
                break;
        }

        if ($resultArray !== []) {
            $this->logger->info(
                sprintf(
                    'Invoked Automatic Order Process with Result %s for oder %s and mode %s',
                    json_encode($resultArray),
                    $order->getNumber(),
                    $mode
                )
            );
        } else {
            $this->logger->error(
                sprintf(
                    'Invoked Automatic Order Process with Invalid Mode %s for oder %s',
                    $mode,
                    $order->getNumber()
                )
            );
        }

        return $processed;
    }
}