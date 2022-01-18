<?php

namespace Payolution\Event;

use Enlight_Event_EventArgs;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Payment\Workflow\WorkflowPositionContext;

/**
 * Class AfterCaptureDoneEvent
 *
 * @package Payolution\Event
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class AfterCaptureDoneEvent extends Enlight_Event_EventArgs
{
    /**
     * Event Name
     *
     * @var string
     */
    const EVENT_NAME = 'polpaymentpayolution.capture_done_request';

    /**
     * @var PayolutionResponse
     */
    private $response;

    /**
     * @var WorkflowPositionContext
     */
    private $positionContext;

    /**
     * @var WorkflowElementsContext
     */
    private $context;

    /**
     * AfterCaptureDoneEvent constructor.
     *
     * @param PayolutionResponse $response
     * @param WorkflowPositionContext $positionContext
     * @param WorkflowElementsContext $context
     */
    public function __construct(
        PayolutionResponse $response,
        WorkflowPositionContext $positionContext,
        WorkflowElementsContext $context
    ) {
        $this->response = $response;
        $this->positionContext = $positionContext;
        $this->context = $context;

        parent::__construct();
    }

    /**
     * Get Response
     *
     * @return PayolutionResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get PositionContext
     *
     * @return WorkflowPositionContext
     */
    public function getPositionContext()
    {
        return $this->positionContext;
    }

    /**
     * Get Context
     *
     * @return WorkflowElementsContext
     */
    public function getContext()
    {
        return $this->context;
    }
}
