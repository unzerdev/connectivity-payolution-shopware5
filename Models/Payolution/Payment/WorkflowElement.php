<?php

namespace PolPaymentPayolution\Models\Payolution\Payment;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class Capture
 *
 * @package PolPaymentPayolution\Models\Payolution\Payment
 *
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 *
 * @ORM\Table(name="payolution_workflow_element")
 * @ORM\Entity(repositoryClass="PolPaymentPayolution\Models\Payolution\Payment\WorkflowRepository")
 */
class WorkflowElement extends ModelEntity
{
    /**
     * Table Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Capture Identifier
     *
     * @var string
     *
     * @ORM\Column(name="identifier", type="string")
     */
    private $identifier;

    /**
     * Additional Capture Identifier
     *
     * @var string
     *
     * @ORM\Column(name="additional_identifier", type="string", nullable=true)
     */
    private $additionalIdentifier;

    /**
     * Capture Amount
     *
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * Capture Position name
     *
     * @var string
     *
     * @ORM\Column(name="`name`", type="string")
     */
    private $name;

    /**
     * Capture Position Quantity
     *
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * Capture Date
     *
     * @var DateTime
     *
     * @ORM\Column(name="created", type="date")
     */
    private $created;

    /**
     * Capture Date
     *
     * @var DateTime
     *
     * @ORM\Column(name="last_modified", type="date")
     */
    private $lastModified;

    /**
     * Is Position already complete captured
     *
     * @var int
     *
     * @ORM\Column(name="`captured`", type="boolean")
     */
    private $captured;

    /**
     * Is Position already complete refunded
     *
     * @var int
     *
     * @ORM\Column(name="`refunded`", type="boolean")
     */
    private $refunded;

    /**
     * Captured Quantity
     *
     * @var int
     *
     * @ORM\Column(name="`captured_quantity`", type="integer")
     */
    private $capturedQuantity;

    /**
     * Captured Quantity
     *
     * @var int
     *
     * @ORM\Column(name="`refunded_quantity`", type="integer")
     */
    private $refundedQuantity;

    /**
     * Order Id
     *
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * Detail Id
     *
     * @var int
     *
     * @ORM\Column(name="order_detail_id", type="integer", nullable=true)
     */
    private $orderDetailId;

    /**
     * Capture constructor.
     *
     * @param DateTime $captureDate
     */
    public function __construct(DateTime $captureDate)
    {
        $this->created = $captureDate;
        $this->captured = 0;
        $this->refunded = 0;
        $this->capturedQuantity = 0;
        $this->refundedQuantity = 0;
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set Identifier
     *
     * @param string $identifier
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get AdditionalIdentifier
     *
     * @return string
     */
    public function getAdditionalIdentifier()
    {
        return $this->additionalIdentifier;
    }

    /**
     * Set AdditionalIdentifier
     *
     * @param string $additionalIdentifier
     *
     * @return self
     */
    public function setAdditionalIdentifier($additionalIdentifier)
    {
        $this->additionalIdentifier = $additionalIdentifier;

        return $this;
    }

    /**
     * Get Amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set Amount
     *
     * @param float $amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set Quantity
     *
     * @param int $quantity
     *
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get Created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Created
     *
     * @param DateTime $created
     *
     * @return self
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get LastModified
     *
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set LastModified
     *
     * @param DateTime $lastModified
     * @return self
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * Get Captured
     *
     * @return boolean
     */
    public function isCaptured()
    {
        return (bool) $this->captured;
    }

    /**
     * Set Captured
     *
     * @param bool $captured
     *
     * @return self
     */
    public function setCaptured($captured)
    {
        $this->captured = (int) $captured;

        return $this;
    }

    /**
     * Get Refunded
     *
     * @return bool
     */
    public function isRefunded()
    {
        return (bool) $this->refunded;
    }

    /**
     * Set Refunded
     *
     * @param bool $refunded
     *
     * @return self
     */
    public function setRefunded($refunded)
    {
        $this->refunded = (int) $refunded;

        return $this;
    }

    /**
     * Get CapturedQuantity
     *
     * @return int
     */
    public function getCapturedQuantity()
    {
        return $this->capturedQuantity;
    }

    /**
     * Set CapturedQuantity
     *
     * @param int $capturedQuantity
     *
     * @return self
     */
    public function setCapturedQuantity($capturedQuantity)
    {
        $this->capturedQuantity = $capturedQuantity;

        return $this;
    }

    /**
     * Get RefundedQuantity
     *
     * @return int
     */
    public function getRefundedQuantity()
    {
        return $this->refundedQuantity;
    }

    /**
     * Set RefundedQuantity
     *
     * @param int $refundedQuantity
     * @return self
     */
    public function setRefundedQuantity($refundedQuantity)
    {
        $this->refundedQuantity = $refundedQuantity;

        return $this;
    }

    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set OrderId
     *
     * @param int $orderId
     *
     * @return self
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get OrderDetailId
     *
     * @return int
     */
    public function getOrderDetailId()
    {
        return $this->orderDetailId;
    }

    /**
     * Set OrderDetailId
     *
     * @param int $orderDetailId
     *
     * @return self
     */
    public function setOrderDetailId($orderDetailId)
    {
        $this->orderDetailId = $orderDetailId;

        return $this;
    }
}