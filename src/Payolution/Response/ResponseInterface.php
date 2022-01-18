<?php

namespace Payolution\Response;

use Payolution\Exception\ResponseParseException;

/**
 * Interface ResponseInterface
 *
 * @package Payolution\Response
 */
interface ResponseInterface
{
    /**
     * Get ResponseData
     *
     * @return string
     * @throws ResponseParseException
     */
    public function getResponseData();
}
