<?php

namespace PolPaymentPayolution\State;

use Shopware\Models\Order\Order;

/**
 * Context for the state change
 *
 * @package PolPaymentPayolution\State
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class StateChangeContext
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $module;

    /**
     * @var null|int
     */
    private $currentOrderStatusId;
    /**
     * @var null|int
     */
    private $newOrderStatusId;

    /**
     * StateChangeContext constructor.
     *
     * @param Order $order
     * @param string $controller
     * @param string $action
     * @param string $module
     * @param null $currentOrderStatusId
     * @param null $newOrderStatusId
     */
    public function __construct(Order $order, $controller, $action, $module, $currentOrderStatusId = null, $newOrderStatusId = null)
    {
        $this->order = $order;
        $this->controller = $controller;
        $this->action = $action;
        $this->module = $module;
        $this->currentOrderStatusId = $currentOrderStatusId;
        $this->newOrderStatusId = $newOrderStatusId;
    }

    /**
     * Get Order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get Controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get Action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get Module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * The getter for currentOrderStatusId
     *
     * @return int|null
     */
    public function getCurrentOrderStatusId()
    {
        return $this->currentOrderStatusId;
    }

    /**
     * The getter for newOrderStatusId
     *
     * @return int|null
     */
    public function getNewOrderStatusId()
    {
        return $this->newOrderStatusId;
    }

    /**
     * Get Snippet for the context init
     *
     * @return string
     */
    public function getContextInitSnippet()
    {
        return sprintf('[%s]%s:%s', $this->module, $this->controller, $this->action);
    }
}
