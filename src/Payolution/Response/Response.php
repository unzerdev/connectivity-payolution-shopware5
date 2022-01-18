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
     * @var mixed
     */
    private $message;

    /**
     * Response constructor.
     *
     * @param mixed $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseData()
    {
        if (!method_exists($this->message, 'getBody')) {
            throw new ResponseParseException('Invalid response found');
        }

        if (!$body = $this->message->getBody()) {
            throw new ResponseParseException('error in response, invalid body');
        }

        return (string) $body->getContents();
    }
}