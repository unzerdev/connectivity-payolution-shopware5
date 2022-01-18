<?php

namespace Payolution\Workflow;

use Payolution\Request\Refund\RequestBuilder;
use Payolution\Request\RequestWrapper;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class RefundRepository
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RefundRepository
{
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var RequestWrapper
     */
    private $requestWrapper;

    /**
     * RefundRepository constructor.
     *
     * @param RequestBuilder $requestBuilder
     * @param RequestWrapper $requestWrapper
     */
    public function __construct(RequestBuilder $requestBuilder, RequestWrapper $requestWrapper)
    {
        $this->requestBuilder = $requestBuilder;
        $this->requestWrapper = $requestWrapper;
    }

    /**
     * Execute Refund By Context
     *
     * @param WorkflowElement $element
     * @param WorkflowElementsContext $context
     * @param int $quantity
     *
     * @return PayolutionResponse
     */
    public function executeRefundByContext(WorkflowElement $element, WorkflowElementsContext $context, $quantity)
    {
        $request = $this->requestBuilder->buildRequest($context, $element, $quantity);

        return new PayolutionResponse(
            $this->requestWrapper->doRequest($request),
            $request
        );
    }

    /**
     * Execute Whole Refund By Context
     *
     * @param WorkflowElementsContext $context
     *
     * @return PayolutionResponse
     */
    public function executeWholeRefund(WorkflowElementsContext $context)
    {
        $request = $this->requestBuilder->buildWholeRequest($context);

        return new PayolutionResponse(
            $this->requestWrapper->doRequest($request),
            $request
        );
    }
}
