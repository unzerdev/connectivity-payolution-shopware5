<?php

namespace Payolution\Response;

use GuzzleHttp\Message\MessageInterface;
use Payolution\Exception\ResponseParseException;

/**
 * Class Response
 *
 * @package Payolution\Response
 */
class Response implements ResponseInterface
{
    /**
     * @var string
     */
    private $data;

    public function __construct(string $message)
    {
        $this->data = $message;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
