<?php

namespace PolPaymentPayolution\Models\Payolution\Payment;

use DateTime;
use Exception;
use Payolution\Response\PayolutionResponse;
use PolPaymentPayolution\Doctrine\EntityManagerWrapper;
use PolPaymentPayolution\Enum\WorkflowType;
use Shopware\Components\Model\ModelRepository;

/**
 * Repository for the workflow  history
 *
 * @package PolPaymentPayolution\Models\Payolution\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowHistoryRepository extends ModelRepository
{
    /**
     * @var EntityManagerWrapper
     */
    private $entityManagerWrapper;

    /**
     * Set EntityManagerWrapper
     *
     * @param EntityManagerWrapper $entityManagerWrapper
     *
     * @return void
     */
    public function setEntityManagerWrapper(EntityManagerWrapper $entityManagerWrapper)
    {
        $this->entityManagerWrapper = $entityManagerWrapper;
    }

    /**
     * Get All Entries by order Id
     *
     * @param int $orderId
     *
     * @return array
     */
    public function getAllEntriesByOrderId($orderId)
    {
        $builder = $this->createQueryBuilder('wh');

        $filter = [
            'orderId' => $orderId,
        ];

        $this->addFilter($builder, $filter);

        return $builder->getQuery()->getResult();
    }

    /**
     * Create Error Entity
     *
     * @param string $message
     * @param float $amount
     * @param int $quantity
     * @param WorkflowElement $element
     *
     * @return void
     * @throws Exception
     */
    public function createRefundErrorEntity($message, $amount, $quantity, WorkflowElement $element)
    {
        $history = new WorkflowHistory(new DateTime());
        $history->setAmount($amount);
        $history->setQuantity($quantity);
        $history->setOrderId($element->getOrderId());
        $history->setName($element->getName());
        $history->setSuccess(false);
        $history->setMessage($message);

        $history->setType(WorkflowType::CAPTURE);

        $this->entityManagerWrapper->flush($history);
    }

    /**
     * Create Capture Entry
     *
     * @param PayolutionResponse $response
     * @param WorkflowElement $element
     * @param int $quantity
     * @return WorkflowHistory
     *
     * @return void
     * @throws Exception
     */
    public function createCaptureEntry(PayolutionResponse $response, WorkflowElement $element, $quantity)
    {
        $model = $this->createBaseEntry($response, $element, $quantity);
        $model->setType(WorkflowType::CAPTURE);

        $this->entityManagerWrapper->flush($model);

        return $model;
    }

    /**
     * Create Refund Entry
     *
     * @param PayolutionResponse $response
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return WorkflowHistory
     *
     * @throws Exception
     */
    public function createRefundEntry(
        PayolutionResponse $response,
        WorkflowElement $element,
        $quantity
    ) {
        $model = $this->createBaseEntry($response, $element, $quantity);
        $model->setType(WorkflowType::REFUND);

        $this->entityManagerWrapper->flush($model);

        return $model;
    }

    /**
     * Create Base Entry
     *
     * @param PayolutionResponse $response
     * @param WorkflowElement $element
     * @param int $quantity
     *
     * @return WorkflowHistory
     * @throws Exception
     */
    private function createBaseEntry(PayolutionResponse $response, WorkflowElement $element, $quantity)
    {
        $amount = ($element->getAmount() / $element->getQuantity()) * $quantity;
        if ($amount <= 0) {
            $amount = $element->getAmount();
        }

        $history = new WorkflowHistory(new DateTime());
        $history->setAmount($amount);
        $history->setQuantity($quantity);
        $history->setOrderId($element->getOrderId());
        $history->setName($element->getName());
        $history->setSuccess($response->isSuccess());
        $history->setMessage($response->getProcessMessage());
        $history->setResponse(json_encode($response->getPayload()));
        $history->setRequest(json_encode($response->getRequest()));

        return $history;
    }
}
