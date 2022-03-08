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
    public function executeRequest(RequestInterface $request): ResponseInterface;
}
