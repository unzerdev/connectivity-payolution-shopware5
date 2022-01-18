<?php

namespace PolPaymentPayolution\Payment\Workflow;

use PolPaymentPayolution\Enum\OrderPosition;
use PolPaymentPayolution\SnippetManager\SnippetManagerInterface;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;

/**
 * Class ElementExtractor
 *
 * @package PolPaymentPayolution\Payment\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ElementExtractor
{
    /**
     * @var WorkflowRepository
     */
    private $workflowRepo;

    /**
     * @var SnippetManagerInterface
     */
    private $snippetManager;

    /**
     * ElementExtractor constructor.
     *
     * @param WorkflowRepository $workflowRepo
     * @param SnippetManagerInterface $snippetManager
     */
    public function __construct(
        WorkflowRepository $workflowRepo,
        SnippetManagerInterface $snippetManager
    ) {
        $this->workflowRepo = $workflowRepo;
        $this->snippetManager = $snippetManager;
    }

    /**
     * Extract Element From Identifier
     *
     * @param int $orderId
     * @param null|string $identifier
     * @param null|string $additionalIdentifier
     *
     * @return null|WorkflowElement
     */
    public function extractElementFromIdentifier($orderId, $identifier = null, $additionalIdentifier = null)
    {
        $elementId = $identifier;
        if (!$identifier && $additionalIdentifier === OrderPosition::SHIPPING_IDENTIFIER) {
            $elementId = OrderPosition::SHIPPING_ID;
        }

        if (!$element = $this->workflowRepo->getElementByIdentifier($elementId, $orderId)) {
            return null;
        }

        return $element;
    }

    /**
     * Extract All Elements from order
     *
     * @param int $orderId
     *
     * @return WorkflowElement[]
     */
    public function extractAllElementsFromOrder($orderId)
    {
        return $this->workflowRepo->getAllElementsForOrder($orderId);
    }

    /**
     * Extract Absolute Capture Elements
     *
     * @param float $amount
     * @param int $orderId
     *
     * @return WorkflowElement
     */
    public function extractAbsoluteCaptureElementFromContext($amount, $orderId)
    {
        //Todo: fix typo
        $name = $this->snippetManager->getByName(
            'payolutionDiffernceName',
            'backend/pol_payment_payolution/differnce',
            'restlicher Betrag'
        );

        return $this->workflowRepo->createAbsolutePosition($amount, $orderId, $name);
    }

    /**
     * Extract Absolute Refund Elements
     *
     * @param float $amount
     * @param int $orderId
     *
     * @return WorkflowElement
     */
    public function extractAbsoluteRefundElementFromContext($amount, $orderId)
    {
        //Todo: fix typo
        $name = $this->snippetManager->getByName(
            'payolutionDiffernceNameRefund',
            'backend/pol_payment_payolution/differnce',
            'Gutschrift'
        );

        return $this->workflowRepo->createAbsoluteRefundElement($amount, $orderId, $name);
    }
}