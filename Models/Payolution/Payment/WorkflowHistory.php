<?php

namespace PolPaymentPayolution\Models\Payolution\Payment;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class WorkflowHistory
 *
 * @package PolPaymentPayolution\Models\Payolution\Payment
 *
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 *
 * @ORM\Table(name="payolution_workflow_history")
 * @ORM\Entity(repositoryClass="PolPaymentPayolution\Models\Payolution\Payment\WorkflowHistoryRepository")
 */
class WorkflowHistory extends ModelEntity
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
     * @ORM\Column(name="`type`", type="string")
     */
    private $type;

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
     * @ORM\Column(name="capture_date", type="date")
     */
    private $captureDate;

    /**
     * Order Id
     *
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * Is Position already captured
     *
     * @var int
     *
     * @ORM\Column(name="`success`", type="boolean")
     */
    private $success;

    /**
     * Capture Response
     *
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    private $message;

    /**
     * Capture Request
     *
     * @var string
     *
     * @ORM\Column(name="request", type="text", nullable=true)
     */
    private $request;

    /**
     * Capture Response
     *
     * @var string
     *
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private $response;

    /**
     * Capture constructor.
     *
     * @param DateTime $captureDate
     */
    public function __construct(DateTime $captureDate)
    {
        $this->captureDate = $captureDate;
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
     * Set Id
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * Get CaptureDate
     *
     * @return DateTime
     */
    public function getCaptureDate()
    {
        return $this->captureDate;
    }

    /**
     * Set CaptureDate
     *
     * @param DateTime $captureDate
     *
     * @return self
     */
    public function setCaptureDate($captureDate)
    {
        $this->captureDate = $captureDate;

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
     * Get Success
     *
     * @return int
     */
    public function isSuccess()
    {
        return filter_var($this->success, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Set Success
     *
     * @param boolean $success
     *
     * @return self
     */
    public function setSuccess($success)
    {
        $this->success = (int) $success;

        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get Request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Request
     *
     * @param string $request
     *
     * @return self
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get Response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set Response
     *
     * @param string $response
     * @return self
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}