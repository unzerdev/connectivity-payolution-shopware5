<?php

namespace Payolution\Workflow;

use PolPaymentPayolution\Payment\Workflow\WorkflowContextProvider;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class CaptureInvoker
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class CaptureInvoker
{
    /**
     * @var CaptureRepository
     */
    private $captureRepository;

    /**
     * @var CaptureSaveHandler
     */
    private $captureSaveHandler;

    /**
     * @var WorkflowContextProvider
     */
    private $contextProvider;

    /**
     * @var WorkflowVoter
     */
    private $workflowVoter;

    /**
     * CaptureInvoker constructor.
     *
     * @param CaptureRepository $captureRepository
     * @param CaptureSaveHandler $captureSaveHandler
     * @param WorkflowContextProvider $contextProvider
     * @param WorkflowVoter $workflowVoter
     */
    public function __construct(
        CaptureRepository $captureRepository,
        CaptureSaveHandler $captureSaveHandler,
        WorkflowContextProvider $contextProvider,
        WorkflowVoter $workflowVoter
    ) {
        $this->captureRepository = $captureRepository;
        $this->captureSaveHandler = $captureSaveHandler;
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
    public function invokeCaptureWholeOrder($orderId)
    {
        if (!$this->workflowVoter->isWholeCaptureAllowed($orderId)) {
            return [];
        }

        $context = $this->contextProvider->getWorkflowWholeOrderContext($orderId);

        return $this->executeWholeCapture($context);
    }

    /**
     * Invoke Capture Absolute Amount
     *
     * @param float $amount
     * @param int $orderId
     *
     * @return array
     */
    public function invokeCaptureAbsoluteAmount($amount, $orderId)
    {
        $context = $this->contextProvider->getWorkflowCaptureAmountContext($amount, $orderId);

        return $this->executeCapture($context);
    }

    /**
     * Invoke Capture Positions
     *
     * @param int $orderId
     * @param array $positions
     *
     * @return array
     */
    public function invokeCapturePositions($orderId, array $positions)
    {
        $context = $this->contextProvider->getWorkflowContextForPositions($orderId, $positions);

        return $this->executeWholeCapture($context);
    }

    /**
     * Invoke Capture Element
     *
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return array
     */
    public function invokeCaptureElement(WorkflowElement $element, $quantity)
    {
        $context = $this->contextProvider->getContextForElement($element, $quantity);

        return $this->executeCapture($context);
    }

    /**
     * Execute Capture
     *
     * @param WorkflowElementsContext $context
     *
     * @return array
     */
    private function executeCapture(WorkflowElementsContext $context)
    {
        $results = [];

        foreach ($context->getElements() as $element) {
            $response = $this->captureRepository->executeCaptureByContext(
                $element->getElement(),
                $context,
                $element->getQuantity()
            );
            $results[] = $this->captureSaveHandler->saveCapture($response, $context, $element);
        }

        return $results;
    }

    /**
     * Execute Capture Whole Order
     *
     * @param WorkflowElementsContext $context
     * @return array
     */
    private function executeWholeCapture(WorkflowElementsContext $context)
    {
        $response = $this->captureRepository->executeWholeCapture($context);

        return $this->captureSaveHandler->saveWholeOrderCapture($response, $context);
    }
}