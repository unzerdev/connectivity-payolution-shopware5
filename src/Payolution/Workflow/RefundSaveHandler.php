<?php

namespace Payolution\Workflow;

use Enlight_Event_EventManager;
use Payolution\Event\AfterRefundDoneEvent;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Doctrine\EntityManagerWrapper;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Payment\Workflow\WorkflowPositionContext;
use Psr\Log\LoggerInterface;

/**
 * Class RefundSaveHandler
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RefundSaveHandler
{
    /**
     * @var EntityManagerWrapper
     */
    private $entityManager;

    /**
     * @var Enlight_Event_EventManager
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RefundSaveHandler constructor.
     *
     * @param EntityManagerWrapper $entityManager
     * @param Enlight_Event_EventManager $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerWrapper $entityManager,
        Enlight_Event_EventManager $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * Save Refund
     *
     * @param PayolutionResponse $response
     * @param WorkflowElementsContext $context
     * @param WorkflowPositionContext $positionContext
     *
     * @return array
     **/
    public function saveRefund(
        PayolutionResponse $response,
        WorkflowElementsContext $context,
        WorkflowPositionContext $positionContext
    ) {
        if ($response->isSuccess()) {
            $this->processElement($response, $context, $positionContext);
        }

        return [
            'request' => $response->getRequest(),
            'response' => $response->getPayload()
        ];
    }

    /**
     * Save Whole Order Refund
     *
     * @param PayolutionResponse $response
     * @param WorkflowElementsContext $context
     *
     * @return array
     */
    public function saveWholeOrderRefund(PayolutionResponse $response, WorkflowElementsContext $context)
    {
        foreach ($context->getElements() as $positionElement) {
            $this->processElement($response, $context, $positionElement);
        }

        if ($response->isSuccess()) {
            $this->entityManager->flush();
        }

        return [
            'request' => $response->getRequest(),
            'response' => $response->getPayload()
        ];
    }

    /**
     * Process Element
     *
     * @param PayolutionResponse $response
     * @param WorkflowElementsContext $context
     * @param WorkflowPositionContext $positionContext
     *
     * @return void
     **/
    private function processElement(
        PayolutionResponse $response,
        WorkflowElementsContext $context,
        WorkflowPositionContext $positionContext
    ) {
        $element = $positionContext->getElement();

        if ($response->isSuccess()) {
            $refundedQuantity = $element->getRefundedQuantity() + $positionContext->getQuantity();
            $element->setRefundedQuantity($refundedQuantity);

            if ($refundedQuantity === $element->getQuantity()) {
                $element->setRefunded(true);
            }

            $this->entityManager->persist($element);
            $this->entityManager->flush($element);
        }

        $event = new AfterRefundDoneEvent($response, $positionContext, $context);

        $this->eventDispatcher->notify(AfterRefundDoneEvent::EVENT_NAME, $event);
    }
}
