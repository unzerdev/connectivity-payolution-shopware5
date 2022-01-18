<?php

namespace PolPaymentPayolution\Models\Payolution\Payment;

use DateTime;
use Exception;
use PolPaymentPayolution\Enum\OrderPosition as OrderPositionEnum;
use PolPaymentPayolution\Payment\Order\OrderPosition;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

/**
 * Class WorkflowRepository
 *
 * @package PolPaymentPayolution\Models\Payolution\Payment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowRepository extends ModelRepository
{
    /**
     * Get All Elements for Order
     * Position Identifier is Array key
     *
     * @param $orderId
     *
     * @return WorkflowElement[]
     */
    public function getAllElementsForOrder($orderId)
    {
        $builder = $this->createQueryBuilder('wf');

        $filter = [
            'orderId' => $orderId,
        ];

        $this->addFilter($builder, $filter);

        $result = $builder->getQuery()->getResult();
        $positions = [];

        /**
         * @var WorkflowElement $value
         */
        foreach ($result as $value) {
            $identifier = $value->getIdentifier() === OrderPositionEnum::DIFFERENCE_PRINCE ||  $value->getIdentifier() === OrderPositionEnum::ORDER_REFUND_IDENTIFIER ?
                 $value->getIdentifier() . '-' . $value->getAdditionalIdentifier() : $identifier = $value->getIdentifier();
            $positions[$identifier] = $value;
        }

        return $positions;
    }

    /**
     * Get Remaining Refund Amount
     *
     * @param int $orderId
     *
     * @return float
     */
    public function getRemainingRefundAmount($orderId)
    {
        $amount = 0;
        /**
         * @var WorkflowElement $element
         */
        foreach ($this->getAllElementsForOrder($orderId) as $element) {
            if ($element->getIdentifier() === OrderPositionEnum::DIFFERENCE_PRINCE && !$element->isRefunded()) {
                $amount += $element->getAmount();
                continue;
            }

            if ($element->getIdentifier() === OrderPositionEnum::ORDER_REFUND_IDENTIFIER) {
                $amount -= $element->getAmount();
            }
        }

        return $amount;
    }

    /**
     * Get Element By Position and Order
     *
     * @param int $positionId
     * @param int $orderId
     *
     * @return WorkflowElement|null
     */
    public function getElementByIdentifier($positionId, $orderId)
    {
        $builder = $this->createQueryBuilder('c');

        $filter = [
            'orderId' => $orderId,
            'identifier' => $positionId
        ];

        $this->addFilter($builder, $filter);

        try {
            $result = $builder->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            return null;
        }

        return $result;
    }

    /**
     * Create Absolute Position
     *
     * @param float $amount
     * @param int $orderId
     * @param string $name
     *
     * @return WorkflowElement
     */
    public function createAbsolutePosition($amount, $orderId, $name)
    {
        $model = new WorkflowElement(new DateTime());
        $model->setOrderId($orderId);
        $model->setLastModified(new DateTime());
        $model->setQuantity(1);
        $model->setName($name);
        $model->setAmount($amount);
        $model->setIdentifier(OrderPositionEnum::DIFFERENCE_PRINCE);
        $model->setAdditionalIdentifier(time());

        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();

        return $model;
    }

    /**
     * Create Refund Position
     *
     * @param float $amount
     * @param int $orderId
     * @param string $name
     * @return WorkflowElement
     */
    public function createAbsoluteRefundElement($amount, $orderId, $name)
    {
        $model = new WorkflowElement(new DateTime());
        $model->setOrderId($orderId);
        $model->setLastModified(new DateTime());
        $model->setQuantity(1);
        $model->setName($name);
        $model->setAmount($amount);
        $model->setIdentifier('order_refund');
        $model->setAdditionalIdentifier(time());
        $model->setCaptured(1);
        $model->setCapturedQuantity(1);

        return $model;
    }

    /**
     * Create from Positions
     *
     * @param array $positions
     * @param Order $order
     *
     * @return array
     */
    public function createFromPositions(array $positions, Order $order)
    {
        return array_map(function (OrderPosition $position) use($order) {
            return $this->createFromPosition($position, $order);
        }, $positions);
    }

    /**
     * Create From Position
     *
     * @param OrderPosition $position
     * @param Order $order
     *
     * @return WorkflowElement
     */
    public function createFromPosition(OrderPosition $position, Order $order)
    {
        // Check if Element already exists
        if ($captureModel = $this->getElementByIdentifier($position->getIdentifier()->getIdentifier(), $order->getId())) {
            return $captureModel;
        }

        $model = new WorkflowElement(new DateTime());
        $model->setOrderId($order->getId());
        $model->setLastModified(new DateTime());
        $model->setQuantity($position->getQuantity());
        $model->setName($position->getName());
        $model->setAmount($position->getAmount()->getValue());
        $model->setIdentifier($position->getIdentifier()->getIdentifier());

        if ($additional = $position->getIdentifier()->getAdditionalIdentifier()) {
            $model->setAdditionalIdentifier($additional);
        }

        if ($detail = $this->getEntityManager()->find(Detail::class, $position->getIdentifier()->getIdentifier())) {
            $model->setOrderDetailId($detail->getId());
        }

        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();
    }
}
