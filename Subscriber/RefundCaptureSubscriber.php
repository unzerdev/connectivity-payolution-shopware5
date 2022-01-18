<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Exception;
use Payolution\Event\AfterCaptureDoneEvent;
use Payolution\Event\AfterRefundDoneEvent;
use PolPaymentPayolution\Enum\PaymentType;
use PolPaymentPayolution\State\OrderStateHandler;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowHistoryRepository;

/**
 * Class RefundCaptureSubscriber
 *
 * Listens to events when a refund or capture is done.
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RefundCaptureSubscriber implements SubscriberInterface
{
    /**
     * @var WorkflowHistoryRepository
     */
    private $workflowHistoryRepository;

    /**
     * @var OrderStateHandler
     */
    private $orderStateHandler;

    /**
     * Workflow constructor.
     *
     * @param WorkflowHistoryRepository $workflowHistoryRepository
     * @param OrderStateHandler $orderStateHandler
     */
    public function __construct(WorkflowHistoryRepository $workflowHistoryRepository, OrderStateHandler $orderStateHandler)
    {
        $this->workflowHistoryRepository = $workflowHistoryRepository;
        $this->orderStateHandler = $orderStateHandler;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @inheritdoc
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AfterRefundDoneEvent::EVENT_NAME => 'onSaveRefund',
            AfterCaptureDoneEvent::EVENT_NAME => 'onCapture',
        ];
    }

    /**
     * Save Refund
     *
     * @param AfterRefundDoneEvent $event
     *
     * @return void
     * @throws Exception
     */
    public function onSaveRefund(AfterRefundDoneEvent $event)
    {
        $response = $event->getResponse();
        $positionContext = $event->getPositionContext();

        $this->workflowHistoryRepository->createRefundEntry(
            $response,
            $positionContext->getElement(),
            $positionContext->getQuantity()
        );

        if ($response->isSuccess()) {
            $this->orderStateHandler->setState(
                $positionContext->getElement()->getOrderId(),
                $event->getContext()->getPluginConfig(),
                PaymentType::REFUND
            );
        }
    }

    /**
     * Save capture
     *
     * @param AfterCaptureDoneEvent $event
     *
     * @return void
     * @throws Exception
     */
    public function onCapture(AfterCaptureDoneEvent $event)
    {
        $response = $event->getResponse();
        $positionContext = $event->getPositionContext();

        $this->workflowHistoryRepository->createCaptureEntry(
            $response,
            $positionContext->getElement(),
            $positionContext->getQuantity()
        );

        if ($response->isSuccess()) {
            $this->orderStateHandler->setState(
                $positionContext->getElement()->getOrderId(),
                $event->getContext()->getPluginConfig(),
                PaymentType::CAPTURE
            );
        }
    }
}