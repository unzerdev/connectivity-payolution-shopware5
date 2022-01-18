<?php

namespace Payolution\Workflow;

use Enlight_Event_EventManager;
use Payolution\Event\AfterCaptureDoneEvent;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Doctrine\EntityManagerWrapper;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Payment\Workflow\WorkflowPositionContext;

/**
 * Class CaptureSaveHandler
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class CaptureSaveHandler
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
     * CaptureSaveHandler constructor.
     *
     * @param EntityManagerWrapper $entityManager
     * @param Enlight_Event_EventManager $eventDispatcher
     */
    public function __construct(EntityManagerWrapper $entityManager, Enlight_Event_EventManager $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Save Capture
     *
     * @param PayolutionResponse $response
     * @param WorkflowElementsContext $context
     * @param WorkflowPositionContext $positionContext
     *
     * @return array
     */
    public function saveCapture(
        PayolutionResponse $response,
        WorkflowElementsContext $context,
        WorkflowPositionContext $positionContext
    ) {

        $this->processElement($response, $context, $positionContext);

        return [
            'request' => $response->getRequest(),
            'response' => $response->getPayload()
        ];
    }

    /**
     * Save Whole Order Capture
     *
     * @param PayolutionResponse $response
     * @param WorkflowElementsContext $context
     *
     * @return array
     */
    public function saveWholeOrderCapture(PayolutionResponse $response, WorkflowElementsContext $context)
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

        if ($response->isSuccess()) {
            $element = $positionContext->getElement();
            $captureQuantity = $element->getCapturedQuantity() + $positionContext->getQuantity();
            $element->setCapturedQuantity($captureQuantity);

            if ($captureQuantity === $element->getQuantity()) {
                $element->setCaptured(true);
            }

            $this->entityManager->flush($element);
        }

        $event = new AfterCaptureDoneEvent($response, $positionContext, $context);

        $this->eventDispatcher->notify(AfterCaptureDoneEvent::EVENT_NAME, $event);
    }
}