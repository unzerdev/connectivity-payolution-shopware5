<?php

namespace Payolution\Response;

/**
 * Class PayolutionResponse
 *
 * @package Payolution\Response
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PayolutionResponse
{
    /**
     * Error Code
     *
     * @var string
     */
    const ERROR_STATUS_CODE = 'ERROR';

    /**
     * Unknown Error Code
     *
     * @var string
     */
    const GENERIC_ERROR_MESSAGE = 'Unknown Error';

    /**
     * @var array
     */
    private $payload;

    /**
     * @var array
     */
    private $request;

    /**
     * CaptureResponse constructor.
     *
     * @param array $payload
     * @param array $request
     */
    public function __construct(array $payload, array $request)
    {
        $this->payload = $payload;
        $this->request = $request;
    }

    /**
     * Is Success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return isset($this->payload['PROCESSING_STATUS_CODE'])
            && ($this->payload['PROCESSING_STATUS_CODE'] === '90' || $this->payload['PROCESSING_STATUS_CODE'] === '00');
    }

    /**
     * Get Status Code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->payload['PROCESSING_STATUS_CODE'] ?: self::ERROR_STATUS_CODE;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getProcessMessage()
    {
        return $this->payload['PROCESSING_RETURN'] ?: self::GENERIC_ERROR_MESSAGE;
    }

    /**
     * Get Payload
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Get Request
     *
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }
}