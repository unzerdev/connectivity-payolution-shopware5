<?php

namespace Payolution\Workflow;

use Doctrine\ORM\EntityManagerInterface;
use PolPaymentPayolution\Payment\Workflow\WorkflowContextProvider;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class RefundInvoker
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RefundInvoker
{
    /**
     * @var RefundSaveHandler
     */
    private $saveRefundHandler;

    /**
     * @var RefundRepository
     */
    private $refundRepository;

    /**
     * @var WorkflowContextProvider
     */
    private $contextProvider;

    /**
     * @var WorkflowVoter
     */
    private $workflowVoter;

    /**
     * RefundInvoker constructor.
     *
     * @param RefundSaveHandler $saveRefundHandler
     * @param RefundRepository $refundRepository
     * @param WorkflowContextProvider $contextProvider
     * @param WorkflowVoter $workflowVoter
     */
    public function __construct(
        RefundSaveHandler $saveRefundHandler,
        RefundRepository $refundRepository,
        WorkflowContextProvider $contextProvider,
        WorkflowVoter $workflowVoter
    ) {
        $this->saveRefundHandler = $saveRefundHandler;
        $this->refundRepository = $refundRepository;
        $this->contextProvider = $contextProvider;
        $this->workflowVoter = $workflowVoter;
    }

    /**
     * Invoke Capture Whole order
     *
     * @param int $orderId
     *
     * @return array
     */
    public function invokeRefundWholeOrder($orderId)
    {
        if (!$this->workflowVoter->isWholeRefundAllowed($orderId)) {
            return [];
        }

        $context = $this->contextProvider->getWorkflowWholeOrderContext($orderId);

        return $this->executeWholeRefund($context);
    }

    /**
     * Invoke Refund Absolute Amount
     *
     * @param float $amount
     * @param int $orderId
     *
     * @return array
     */
    public function invokeRefundAbsoluteAmount($amount, $orderId)
    {
        $context = $this->contextProvider->getWorkflowRefundAmountContext($amount, $orderId);

        return $this->executeRefund($context);
    }

    /**
     * Invoke Refund Positions
     *
     * @param int $orderId
     * @param array $positions
     *
     * @return array
     */
    public function invokeRefundPositions($orderId, array $positions)
    {
        $context = $this->contextProvider->getWorkflowContextForPositions($orderId, $positions);

        return $this->executeWholeRefund($context);
    }

    /**
     * Invoke Refund Element
     *
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return array
     */
    public function invokeRefundElement(WorkflowElement $element, $quantity)
    {
        $context = $this->contextProvider->getContextForElement($element, $quantity);

        return $this->executeRefund($context);
    }

    /**
     * Execute refund
     *
     * @param WorkflowElementsContext $context
     *
     * @return array
     */
    private function executeRefund(WorkflowElementsContext $context)
    {
        $results = [];

        foreach ($context->getElements() as $element) {
            $response = $this->refundRepository->executeRefundByContext(
                $element->getElement(),
                $context,
                $element->getQuantity()
            );
            $results[] = $this->saveRefundHandler->saveRefund($response, $context, $element);
        }

        return $results;
    }

    /**
     * Execute Refund Whole Order.
     *
     * @param WorkflowElementsContext $context
     *
     * @return array
     */
    private function executeWholeRefund(WorkflowElementsContext $context)
    {
        $response = $this->refundRepository->executeWholeRefund($context);

        return $this->saveRefundHandler->saveWholeOrderRefund($response, $context);
    }
}
