<?php

namespace Payolution\Request\Capture;

use Payolution\Request\Builder\Mapper\SystemMapper;
use Payolution\Request\Builder\Mapper\WorkflowMapper;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class RequestBuilder
 *
 * @package Payolution\Request\Capture
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RequestBuilder
{
    /**
     * @var SystemMapper
     */
    private $systemMapper;

    /**
     * @var WorkflowMapper
     */
    private $workflowMapper;

    /**
     * RequestBuilder constructor.
     *
     * @param SystemMapper $systemMapper
     * @param WorkflowMapper $workflowMapper
     */
    public function __construct(SystemMapper $systemMapper, WorkflowMapper $workflowMapper)
    {
        $this->systemMapper = $systemMapper;
        $this->workflowMapper = $workflowMapper;
    }

    /**
     * Build Request
     *
     * @param WorkflowElementsContext $context
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return array
     */
    public function buildRequest(WorkflowElementsContext $context, WorkflowElement $element, $quantity)
    {
        $amount = ($element->getAmount() / $element->getQuantity()) * $quantity;
        if ($amount <= 0) {
            $amount = $element->getAmount();
        }

        $request = [];
        $this->systemMapper->mapCaptureRequest($context, $request);
        $this->workflowMapper->mapRequest($element->getOrderId(), $amount, $request);

        ksort($request);

        return $request;
    }

    /**
     * Build whole capture request
     *
     * @param WorkflowElementsContext $context
     * @return array
     */
    public function buildWholeRequest(WorkflowElementsContext $context)
    {
        $amount = 0;

        $orderId = null;
        foreach ($context->getElements() as $element) {
            $amount += ($element->getElement()->getAmount() / $element->getElement()->getQuantity()) * $element->getQuantity();
            $orderId = $element->getElement()->getOrderId();
        }

        $request = [];
        $this->systemMapper->mapCaptureRequest($context, $request);
        $this->workflowMapper->mapRequest($orderId, $amount, $request);

        ksort($request);

        return $request;
    }
}