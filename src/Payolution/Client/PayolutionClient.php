<?php

namespace Payolution\Client;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Url;
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
    /** @var LoggerInterface */
    private $logger;

    /** @var SessionRequestDecorator */
    private $requestDecorator;

    /** @var LatinConverter */
    private $converter;

    /** @var int */
    private $retryCounter = 0;

    public function __construct(
        LoggerInterface $logger,
        SessionRequestDecorator $requestDecorator,
        LatinConverter $converter
    ) {
        $this->logger           = $logger;
        $this->requestDecorator = $requestDecorator;
        $this->converter        = $converter;
    }

    public function executeRequest(RequestInterface $request): ResponseInterface
    {
        $this->decoratePayload($request);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);

        if ($request->getMethod() === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request->getPayload()['body']));
        }

        if (!empty($request->getPayload()['auth'])) {
            curl_setopt($curl, CURLOPT_USERPWD, implode(':', $request->getPayload()['auth']));
        }

        curl_setopt($curl, CURLOPT_URL, $request->getEndPoint());

        $rawResponse = (string)curl_exec($curl);
        $curlStatus  = curl_errno($curl);
        $httpStatus  = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($curlStatus !== CURLE_OK || $httpStatus === 500) {
            return $this->retryRequest($request);
        }

        return new Response($rawResponse);
    }

    private function retryRequest(RequestInterface $request): ResponseInterface
    {
        ++$this->retryCounter;

        if ($this->retryCounter >= 3) {
            $this->logger->error('Error in payment client', [
                'request' => $request,
            ]);
            throw new ClientException('Error in payment client');
        }

        return $this->executeRequest($request);
    }

    private function decoratePayload(RequestInterface $request): void
    {
        $payload = $request->getRequestType() === RequestEnums::CI_TYPE
            ? $this->requestDecorator->appendSessionCI($request->getPayload())
            : $this->requestDecorator->appendSession($request->getPayload());

        $payload = $this->converter->convert($payload);

        $request->setPayload($payload);
    }
}
