<?php

namespace Payolution\Client;

use GuzzleHttp\Exception\TransferException;
use Payolution\Converter\LatinConverter;
use Payolution\Exception\ClientException;
use Payolution\Request\RequestEnums;
use Payolution\Request\RequestInterface;
use Payolution\Response\Response;
use Payolution\Response\ResponseInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Payolution\Session\SessionRequestDecorator;
use Psr\Log\LoggerInterface;

/**
 * Class PayolutionClient
 *
 * @package Payolution\Client
 */
class PayolutionClient implements ClientInterface
{
    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SessionRequestDecorator
     */
    private $requestDecorator;

    /**
     * @var LatinConverter
     */
    private $converter;

    /**
     * PayolutionClient constructor.
     *
     * @param GuzzleClient $guzzleClient
     * @param LoggerInterface $logger
     * @param SessionRequestDecorator $requestDecorator
     * @param LatinConverter $converter
     */
    public function __construct(
        GuzzleClient $guzzleClient,
        LoggerInterface $logger,
        SessionRequestDecorator $requestDecorator,
        LatinConverter $converter
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->logger = $logger;
        $this->requestDecorator = $requestDecorator;
        $this->converter = $converter;
    }

    /**
     * Executes the given request
     *
     * @param RequestInterface $request
     *
     * @param int $try
     * @return ResponseInterface
     *
     * @throws ClientException
     */
    public function executeRequest(RequestInterface $request, $try = 0)
    {
        $type = $request->getMethod();

        $this->decoratePayload($request);
        $requestInterface = $this->guzzleClient->createRequest(
            $type,
            $request->getEndPoint(),
            $request->getPayload()
        );

        try {
            $response = $this->guzzleClient->send($requestInterface);
        } catch (TransferException $e) {
            //Retry three times on exception
            $try++;
            if ($try <= 2) {
                $this->logger->warning('retry request');
                return $this->executeRequest($request, $try);
            }

            $message = $this->getGuzzleError($e);
            $this->logger->error($message);
            throw new ClientException($message);
        }

        if (!$response->getBody()) {
            throw new ClientException('error in payment client response, invalid body');
        }

        return new Response($response);
    }

    /**
     * Log Guzzle Exception to Logger
     *
     * @param TransferException $exception
     * @return string
     */
    private function getGuzzleError(TransferException $exception)
    {
        $body = '';
        $headers = [];

        if ($exception instanceof RequestException && $exception->hasResponse()) {
            $body = (string) $exception->getResponse()->getBody();
            $headers = (array) $exception->getResponse()->getHeaders();
        }

        $message = sprintf(
            'error in payment client Response Header "%s" Response Body "%s" with error "%s"',
            json_encode($headers),
            $body,
            $exception->getMessage()
        );

        return $message;
    }

    /**
     * Decorate Payload
     *
     * @param RequestInterface $request
     *
     * @return void
     */
    private function decoratePayload(RequestInterface $request)
    {
        $payload = $request->getPayload();

        if ($request->getRequestType() === RequestEnums::CI_TYPE) {
            $payload = $this->requestDecorator->appendSessionCI($payload);
        } else {
            $payload = $this->requestDecorator->appendSession($payload);
        }

        // Convert values to latin
        $payload = $this->converter->convert($payload);

        $request->setPayload($payload);
    }
}
