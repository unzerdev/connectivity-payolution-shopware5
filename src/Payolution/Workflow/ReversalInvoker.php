<?php

namespace Payolution\Workflow;

use PolPaymentPayolution\Backend\Payment\Reversal;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\Doctrine\EntityManagerWrapper;
use PolPaymentPayolution\Enum\PaymentType;
use PolPaymentPayolution\State\OrderStateHandler;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;

/**
 * Class ReversalInvoker
 *
 * Wrapper for legacy reversal call
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ReversalInvoker
{
    /**
     * @var Reversal
     */
    private $executeReversal;

    /**
     * @var WorkflowRepository
     */
    private $workflowRepository;

    /**
     * @var EntityManagerWrapper
     */
    private $entityManagerWrapper;

    /**
     * @var OrderStateHandler
     */
    private $orderStateHandler;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * ReversalInvoker constructor.
     *
     * @param Reversal $executeReversal
     * @param WorkflowRepository $workflowRepository
     * @param EntityManagerWrapper $entityManagerWrapper
     * @param OrderStateHandler $orderStateHandler
     * @param PluginConfig $pluginConfig
     */
    public function __construct(
        Reversal $executeReversal,
        WorkflowRepository $workflowRepository,
        EntityManagerWrapper $entityManagerWrapper,
        OrderStateHandler $orderStateHandler,
        PluginConfig $pluginConfig
    ) {
        $this->executeReversal = $executeReversal;
        $this->workflowRepository = $workflowRepository;
        $this->entityManagerWrapper = $entityManagerWrapper;
        $this->orderStateHandler = $orderStateHandler;
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * Invoke Reversal
     *
     * @param int $orderId
     *
     * @return array
     */
    public function invokeReversal($orderId)
    {
        $result = $this->executeReversal->Request($orderId);

        if (isset($result['success']) && $result['success']) {
            $elements = $this->workflowRepository->getAllElementsForOrder($orderId);

            // Set Positions as Refunded and Captured
            foreach ($elements as $element) {
                $element->setCapturedQuantity($element->getQuantity());
                $element->setRefundedQuantity($element->getQuantity());
                $element->setCaptured(true);
                $element->setRefunded(true);

                $this->entityManagerWrapper->flush($element);
            }

            // Set Order State for reversal
            $this->orderStateHandler->setState($orderId, $this->pluginConfig, PaymentType::REVERSAL);
        }

        return $result;
    }
}