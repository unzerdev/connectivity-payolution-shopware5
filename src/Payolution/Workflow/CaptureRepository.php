<?php

namespace Payolution\Workflow;

use Payolution\Request\Capture\RequestBuilder;
use Payolution\Request\RequestWrapper;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Payment\Workflow\WorkflowElementsContext;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;

/**
 * Class CaptureRepository
 *
 * @package Payolution\Workflow
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class CaptureRepository
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
     * CaptureRepository constructor.
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
     * Execute Capture By Context
     *
     * @param WorkflowElement $element
     * @param WorkflowElementsContext $context
     * @param int $quantity
     *
     * @return PayolutionResponse
     */
    public function executeCaptureByContext(WorkflowElement $element, WorkflowElementsContext $context, $quantity)
    {
        $request = $this->requestBuilder->buildRequest($context, $element, $quantity);

        return new PayolutionResponse(
            $this->requestWrapper->doRequest($request),
            $request
        );
    }

    /**
     * Execute Whole Capture By Context
     *
     * @param WorkflowElementsContext $context
     *
     * @return PayolutionResponse
     */
    public function executeWholeCapture(WorkflowElementsContext $context)
    {
        $request = $this->requestBuilder->buildWholeRequest($context);

        return new PayolutionResponse(
            $this->requestWrapper->doRequest($request),
            $request
        );
    }
}