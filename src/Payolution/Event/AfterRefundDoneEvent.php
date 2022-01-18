<?php

namespace Payolution\Event;

use Doctrine\ORM\EntityManagerInterface;
use Enlight_Event_EventArgs;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Payment\Workflow\WorkflowPositionContext;

/**
 * Class AfterRefundDoneEvent
 *
 * @package Payolution\Event
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class AfterRefundDoneEvent extends Enlight_Event_EventArgs
{
    /**
     * Event Name
     *
     * @var string
     */
    const EVENT_NAME = 'polpaymentpayolution.refund_done_request';

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
     * @var EntityManagerInterface|null
     */
    private $entityManager;

    /**
     * AfterRefundDoneEvent constructor.
     *
     * @param PayolutionResponse $response
     * @param WorkflowPositionContext $positionContext
     * @param WorkflowElementsContext $context
     * @param EntityManagerInterface|null $entityManager
     */
    public function __construct(
        PayolutionResponse $response,
        WorkflowPositionContext $positionContext,
        WorkflowElementsContext $context,
        EntityManagerInterface $entityManager = null
    ) {
        $this->response = $response;
        $this->positionContext = $positionContext;
        $this->context = $context;
        $this->entityManager = $entityManager;

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

    /**
     * Get EntityManager
     *
     * @return EntityManagerInterface|null
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
