<?php

namespace PolPaymentPayolution\Payment\Workflow;

use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class WorkflowPositionContext
 *
 * @package PolPaymentPayolution\Payment\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowPositionContext
{
    /**
     * @var WorkflowElement
     */
    private $element;

    /**
     * @var int
     */
    private $quantity;

    /**
     * WorkflowPositionContext constructor.
     *
     * @param WorkflowElement $element
     * @param int $quantity
     */
    public function __construct(WorkflowElement $element, $quantity)
    {
        $this->element = $element;
        $this->quantity = $quantity;
    }

    /**
     * Get Element
     *
     * @return WorkflowElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Get Quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}