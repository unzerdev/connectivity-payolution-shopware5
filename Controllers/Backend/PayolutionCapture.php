<?php

use Payolution\Workflow\CaptureInvoker;
use PolPaymentPayolution\Normalizer\PositionNormalizer;
use PolPaymentPayolution\Payment\Order\PositionProvider;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Shopware_Controllers_Backend_PayolutionCapture
 *
 * Provides function for capture actions.
 *
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Shopware_Controllers_Backend_PayolutionCapture extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * @var PositionProvider
     */
    private $positionProvider;

    /**
     * @var CaptureInvoker
     */
    private $captureInvoker;

    /**
     * Get PositionProvider
     *
     * @return PositionProvider
     */
    private function getPositionProvider()
    {
        if (!$this->positionProvider) {
            $this->positionProvider = $this->container->get('pol_payment_payolution.payment.order.position_provider');
        }

        return $this->positionProvider;
    }

    /**
     * Get CaptureInvoker
     *
     * @return CaptureInvoker
     */
    private function getCaptureInvoker()
    {
        if (!$this->captureInvoker) {
            $this->captureInvoker = $this->container->get('payolution.workflow.capture_invoker');
        }

        return $this->captureInvoker;
    }

    /**
     * Get Capture Result for Order
     *
     * @return void
     *
     * @throws HttpException
     */
    public function getCapturesAction()
    {
        if (!$orderId = $this->Request()->get('id')) {
            throw new HttpException('order id is required');
        }

        $collection = $this->getPositionProvider()->getCaptureCollection($orderId);

        $serializer = new Serializer([new PositionNormalizer()]);

        $this->View()->assign([
            'total' => $collection->count(),
            'success' => $collection->count() > 0,
            'data' => $serializer->normalize($collection->getPositions())
        ]);
    }

    /**
     * Capture given Positions
     *
     * @return void
     * @throws HttpException
     */
    public function createCapturePositionsAction()
    {
        if (!$orderId = $this->Request()->getPost('orderId')) {
            throw new HttpException('order id is required');
        }

        $positions = $this->Request()->getPost('positions');
        $positions = json_decode($positions, true);

        $results = $this->getCaptureInvoker()->invokeCapturePositions($orderId, $positions);

        $this->View()->assign($results);
    }

    /**
     * Orders Action
     *
     * @return void
     */
    public function orderAction()
    {
    }

    /**
     * Create Absolute Capture
     *
     * @return void
     *
     * @throws HttpException
     */
    public function createCaptureAbsoluteAction()
    {
        if (!$orderId = $this->Request()->get('orderId')) {
            throw new HttpException('order id is required');
        }

        if (!$amount = $this->Request()->get('amount')) {
            throw new HttpException('amount is required');
        }

        $result = $this->getCaptureInvoker()->invokeCaptureAbsoluteAmount($amount, $orderId);

        $this->View()->assign($result);
    }
}