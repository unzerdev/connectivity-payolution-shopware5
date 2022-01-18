<?php

use Payolution\Workflow\RefundInvoker;
use PolPaymentPayolution\Normalizer\PositionNormalizer;
use PolPaymentPayolution\Payment\Order\PositionProvider;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Shopware_Controllers_Backend_PayolutionRefund
 *
 * Provides functions for refund actions.
 *
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Shopware_Controllers_Backend_PayolutionRefund extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * @var PositionProvider
     */
    private $orderPositionProvider;

    /**
     * @var RefundInvoker
     */
    private $refundInvoker;

    /**
     * Get OrderPositionProvider
     *
     * @return PositionProvider
     */
    private function getOrderPositionProvider()
    {
        if (!$this->orderPositionProvider) {
            $this->orderPositionProvider = $this->container->get('pol_payment_payolution.payment.order.position_provider');
        }

        return $this->orderPositionProvider;
    }

    /**
     * Get RefundInvoker
     *
     * @return RefundInvoker
     */
    private function getRefundInvoker()
    {
        if (!$this->refundInvoker) {
            $this->refundInvoker = $this->container->get('payolution.workflow.refund_invoker');
        }

        return $this->refundInvoker;
    }


    /**
     * Get Refunds
     *
     * @return void
     *
     * @throws HttpException
     */
    public function getRefundsAction()
    {
        if (!$orderId = $this->Request()->get('id')) {
            throw new HttpException('order id is required');
        }

        $collection = $this->getOrderPositionProvider()->getRefundCollection($orderId);

        $serializer = new Serializer([new PositionNormalizer()]);

        $this->View()->assign([
            'total' => $collection->count(),
            'success' => $collection->count() > 0,
            'data' => $serializer->normalize($collection->getPositions())
        ]);
    }

    /**
     * Order Action (do not remove)
     *
     * @return void
     */
    public function orderAction()
    {
    }

    /**
     * Create Refund Positions
     *
     * @return void
     *
     * @throws HttpException
     */
    public function createRefundPositionsAction()
    {
        if (!$orderId = $this->Request()->getPost('orderId')) {
            throw new HttpException('order id is required');
        }

        $positions = $this->Request()->getPost('positions');
        $positions = json_decode($positions, true);

        $results = $this->getRefundInvoker()->invokeRefundPositions($orderId, $positions);

        $this->View()->assign($results);
    }

    /**
     * Create Absolute Refund
     *
     * @return void
     *
     * @throws HttpException
     */
    public function createRefundAbsoluteAction()
    {
        if (!$orderId = $this->Request()->get('orderId')) {
            throw new HttpException('order id is required');
        }

        if (!$amount = $this->Request()->get('amount')) {
            throw new HttpException('amount is required');
        }

        $result = $this->getRefundInvoker()->invokeRefundAbsoluteAmount($amount, $orderId);

        $this->View()->assign($result);
    }
}