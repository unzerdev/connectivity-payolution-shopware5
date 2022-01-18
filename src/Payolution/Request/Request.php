<?php

namespace Payolution\Request;

use Payolution\Request\Model\RequestOptions;

/**
 * Class Request
 *
 * @package Payolution\Request
 */
class Request implements RequestInterface
{
    /**
     * @var array
     */
    private $payload;

    /**
     * @var RequestOptions
     */
    private $requestOptions;

    /**
     * Request constructor.
     *
     * @param array $payload
     * @param RequestOptions $requestOptions
     */
    public function __construct(array $payload, RequestOptions $requestOptions)
    {
        $this->payload = $payload;
        $this->requestOptions = $requestOptions;
    }

    /**
     * Set Payload
     *
     * @param array $payload
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * get Payload
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @inheritDoc
     */
    public function getEndPoint()
    {
        return $this->requestOptions->getEndPoint();
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return $this->requestOptions->getMethod();
    }

    /**
     * @inheritDoc
     */
    public function getRequestType()
    {
        return $this->requestOptions->getType();
    }
}