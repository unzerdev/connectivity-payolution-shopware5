<?php

namespace Payolution\Client;

use Payolution\Request\RequestInterface;
use Payolution\Response\ResponseInterface;

/**
 * Interface ClientInterface
 *
 * @package Payolution\Client
 */
interface ClientInterface
{
    /**
     * Executes the given request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function executeRequest(RequestInterface $request);
}
