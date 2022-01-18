<?php

namespace PolPaymentPayolution\Payment\Workflow;

use Payolution\Config\Config;
use PolPaymentPayolution\Config\ConfigContext;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\Payment\Order\PositionProvider;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class WorkflowContextProvider
 *
 * @package PolPaymentPayolution\Payment\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowContextProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * @var ConfigContext
     */
    private $configContext;

    /**
     * @var ElementExtractor
     */
    private $elementExtractor;

    /**
     * @var PositionProvider
     */
    private $positionProvider;

    /**
     * WorkflowContextProvider constructor.
     *
     * @param Config $config
     * @param PluginConfig $pluginConfig
     * @param ConfigContext $configContext
     * @param ElementExtractor $elementExtractor
     * @param PositionProvider $positionProvider
     */
    public function __construct(
        Config $config,
        PluginConfig $pluginConfig,
        ConfigContext $configContext,
        ElementExtractor $elementExtractor,
        PositionProvider $positionProvider
    ) {
        $this->config = $config;
        $this->pluginConfig = $pluginConfig;
        $this->configContext = $configContext;
        $this->elementExtractor = $elementExtractor;
        $this->positionProvider = $positionProvider;
    }

    /**
     * Get Workflow Amount Context
     *
     * @param float $amount
     * @param int $orderId
     *
     * @return WorkflowElementsContext
     */
    public function getWorkflowCaptureAmountContext($amount, $orderId)
    {
        $element = $this->elementExtractor->extractAbsoluteCaptureElementFromContext($amount, $orderId);

        return new WorkflowElementsContext(
            $this->config,
            $this->configContext->getShop(),
            $this->pluginConfig,
            [
               new WorkflowPositionContext($element, $element->getQuantity())
            ]
        );
    }

    /**
     * Get Workflow Refund Amount Context
     *
     * @param float $amount
     * @param int $orderId
     *
     * @return WorkflowElementsContext
     */
    public function getWorkflowRefundAmountContext($amount, $orderId)
    {
        $element = $this->elementExtractor->extractAbsoluteRefundElementFromContext($amount, $orderId);

        return new WorkflowElementsContext(
            $this->config,
            $this->configContext->getShop(),
            $this->pluginConfig,
            [
                new WorkflowPositionContext($element, $element->getQuantity())
            ]
        );
    }

    /**
     * Get Workflow Context for Whole Order
     *
     * @param int $orderId
     *
     * @return WorkflowElementsContext
     */
    public function getWorkflowWholeOrderContext($orderId)
    {
        $elements = $this->elementExtractor->extractAllElementsFromOrder($orderId);

        if (count($elements) === 0) {
            $this->positionProvider->getCaptureCollection($orderId);
        }

        $elements = $this->elementExtractor->extractAllElementsFromOrder($orderId);

        return new WorkflowElementsContext(
            $this->config,
            $this->configContext->getShop(),
            $this->pluginConfig,
            array_map(function (WorkflowElement $element) {
                return new WorkflowPositionContext($element, $element->getQuantity());
            }, $elements)
        );
    }

    /**
     * Get Workflow Context for Positions
     *
     * @param int $orderId
     * @param array $positions
     *
     * @return WorkflowElementsContext
     */
    public function getWorkflowContextForPositions($orderId, array $positions)
    {
        $elements = [];

        foreach ($positions as $position) {
            $id = $position['id'] ?: null;
            $additionalId = $position['additionalId'] ?: null;
            $quantity = $position['quantity'] ?: 0;

            if ($quantity > 0) {
                $element = $this->elementExtractor->extractElementFromIdentifier($orderId, $id, $additionalId);

                $elements [] = new WorkflowPositionContext($element, $quantity);
            }
        }

        return new WorkflowElementsContext(
            $this->config,
            $this->configContext->getShop(),
            $this->pluginConfig,
            $elements
        );
    }

    /**
     * Get Context for Element
     *
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return WorkflowElementsContext
     */
    public function getContextForElement(WorkflowElement $element, $quantity)
    {
        return new WorkflowElementsContext(
            $this->config,
            $this->configContext->getShop(),
            $this->pluginConfig,
            [
                new WorkflowPositionContext($element, $quantity)
            ]
        );
    }


}