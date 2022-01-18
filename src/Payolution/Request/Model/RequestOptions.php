<?php

namespace Payolution\Request\Model;

/**
 * Class RequestOptions
 *
 * @package Payolution\Request\Model
 */
class RequestOptions
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $endPoint;

    /**
     * RequestOptions constructor.
     *
     * @param string $method
     * @param string $type
     * @param string $endPoint
     */
    public function __construct($method, $type, $endPoint)
    {
        $this->method = $method;
        $this->type = $type;
        $this->endPoint = $endPoint;
    }

    /**
     * get Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * get EndPoint
     *
     * @return string
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }
}