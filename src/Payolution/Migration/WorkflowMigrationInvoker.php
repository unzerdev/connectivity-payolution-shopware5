<?php

namespace Payolution\Migration;

use Generator;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Payment\Order\PositionProvider;
use Psr\Log\LoggerInterface;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository;
use Shopware\Models\Order\Order;

/**
 * Class WorkflowMigrationInvoker
 *
 * @package Payolution\Migration
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowMigrationInvoker
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var PositionProvider
     */
    private $positionsProvider;

    /**
     * @var  WorkflowRepository
     */
    private $workflowRepository;

    /**
     * @var  LoggerInterface
     */
    private $logger;

    /**
     * WorkflowMigrationInvoker constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param PositionProvider $positionsProvider
     * @param WorkflowRepository $workflowRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ComponentManagerInterface $componentManager,
        PositionProvider $positionsProvider,
        WorkflowRepository $workflowRepository,
        LoggerInterface $logger
    ) {
        $this->componentManager = $componentManager;
        $this->positionsProvider = $positionsProvider;
        $this->workflowRepository = $workflowRepository;
        $this->logger = $logger;
    }

    /**
     * Invoke Migration
     */
    public function invokeMigration()
    {
        $this->logger->info('Fetch Migration Orders');

        foreach ($this->fetchOrders() as $order) {
            $this->migrateOrder($order);
        }
    }

    /**
     * fetch orders
     *
     * @return Generator|Order[]
     */
    private function fetchOrders()
    {
        $query =  $this
            ->componentManager
            ->getModelManager()
            ->getRepository(Order::class)
            ->createQueryBuilder('o')
            ->leftJoin('o.payment', 'p')
            ->leftJoin('o.attribute', 'a')
            ->addSelect('p', 'a');

        $iterable = $query->getQuery()->iterate();
        $fetched = 1;
        $processed = 0;
        /** @var array $order */
        while (($order = $iterable->next()) !== false) {
            if (!isset($order[0])) {
                continue;
            }

            /** @var Order $order */
            $order = $order[0];

            $this->logger->info(sprintf('Fetched/processed "%s/%s" orders', $fetched, $processed));
            $fetched++;

            $payment = $order->getPayment();
            if (!$payment || $payment->getAction() !== 'PolPaymentPayolution' || (string) $order->getNumber() === '0') {
                $this->logger->debug('skip invalid order');
                continue;
            }

            $processed++;
            yield $order;

        }
    }

    /**
     * Migrate Order
     *
     * @param Order $order
     *
     * @return void
     */
    private function migrateOrder(Order $order)
    {
        $attributes = $order->getAttribute();

        $captureAmount = (float) $attributes->getPayolutionCapture();
        $refundAmount = (float) $attributes->getPayolutionRefund();
        $orderAmount = (float) $order->getInvoiceAmount();

        $this->logger->debug(
            sprintf(
                'Migrate Order %s with CaptureAmount %s, Refundamount %s and orderAmount %s',
                $order->getNumber(),
                $captureAmount,
                $refundAmount,
                $orderAmount
            )
        );

        $this->positionsProvider->getCaptureCollection($order->getId());

        $elements = $this->workflowRepository->getAllElementsForOrder($order->getId());

        if ($captureAmount === $orderAmount) {
            $this->logger->debug('Order Captured');
            $this->setElementsCaptured($elements);
        }

        if ($refundAmount === $orderAmount) {
            $this->logger->debug('Order refunded');
            $this->setElementsRefunded($elements);
        }

        $this->componentManager->getModelManager()->flush();
    }

    /**
     * Set Elements captured
     *
     * @param WorkflowElement[] $workflowElements
     *
     * @return void
     */
    private function setElementsCaptured(array $workflowElements)
    {
        foreach ($workflowElements as $element) {
            $element->setCapturedQuantity($element->getQuantity());
            $element->setCaptured(true);
        }
    }

    /**
     * Set Elements refunded
     *
     * @param WorkflowElement[] $workflowElements
     *
     * @return void
     */
    private function setElementsRefunded(array $workflowElements)
    {
        foreach ($workflowElements as $element) {
            $element->setRefundedQuantity($element->getQuantity());
            $element->setRefunded(true);
        }
    }
}