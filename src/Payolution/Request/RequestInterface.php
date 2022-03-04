<?php

namespace Payolution\Request;

/**
 * Interface RequestInterface
 *
 * @package Payolution\Request
 */
interface RequestInterface
{
    /**
     * Returns the payload
     *
     * @return array
     */
    public function getPayload();

    /**
     * Set Payload
     *
     * @param array $payload
     */
    public function setPayload(array $payload);

    /**
     * Returns the request endpoint
     *
     * @return string
     */
    public function getEndPoint();

    /**
     * Returns the request method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Returns the request type
     *
     * @return string
     */
    public function getRequestType();
}
