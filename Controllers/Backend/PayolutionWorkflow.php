<?php

use PolPaymentPayolution\Payment\Capture\WorkflowAmountProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Shopware_Controllers_Backend_PayolutionWorkflow
 *
 * Provides function for workflow actions
 *
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Shopware_Controllers_Backend_PayolutionWorkflow extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * @var WorkflowAmountProvider
     */
    private $amountProvider;

    /**
     * Get AmountProvider
     *
     * @return WorkflowAmountProvider
     */
    private function getAmountProvider()
    {
        if (!$this->amountProvider) {
            $this->amountProvider = $this->container->get('pol_payment_payolution.payment.capture.workflow_amount_provider');
        }

        return $this->amountProvider;
    }

    /**
     * Workflow State Action
     *
     * @return void
     *
     * @throws HttpException
     */
    public function workflowStateAction()
    {
        if (!$orderId = $this->Request()->get('orderId')) {
            throw new HttpException('order id ist required');
        }

        if (!$amount = $this->getAmountProvider()->getWorkflowAmount($orderId)) {
            throw new HttpException('invalid order Id');
        }

        $this->View()->assign([
            'success' => true,
            'refundActive' => $amount->isRefundActive(),
            'captureActive' => $amount->isCaptureActive(),
            'captureAmount' => $amount->getCaptureSnippet(),
            'refundAmount' => $amount->getRefundSnippet()
        ]);
    }
}