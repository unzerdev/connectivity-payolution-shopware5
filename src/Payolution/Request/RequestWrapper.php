<?php

namespace Payolution\Request;

use Exception;
use Payolution\Client\ClientInterface;
use Payolution\Config\Config;
use Payolution\Exception\ClientException;
use Payolution\Exception\ResponseParseException;
use Payolution\Request\Log\LogMessageFactory;
use Payolution\Request\Model\RequestOptions;
use Psr\Log\LoggerInterface;

/**
 * Class RequestWrapper
 *
 * @package Payolution\Request
 */
class RequestWrapper
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RequestWrapper constructor.
     *
     * @param Config $config
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     */
    public function __construct(Config $config, ClientInterface $client, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Do Request
     *
     * @param array|string $data
     * @param string $type
     * @return array|mixed
     *
     * @see RequestEnums
     */
    public function doRequest($data, $type = RequestEnums::REQUEST_TYPE)
    {
        if ($type === RequestEnums::CI_TYPE) {
            $data = [
                'auth' => [
                    $this->config->getInstallmentPayolutionUser(),
                    $this->config->getInstallmentPayolutionPassword()
                ],
                'body' => [
                    'payload' => $data
                ]
            ];
            $url = $this->config->isTestmode()
                ? RequestEnums::getTestEndPoint(true) : RequestEnums::getProdEndpoint(true);
        } else {
            $data = [
                'body' => $data
            ];

            $url = $this->config->isTestmode()
                ? RequestEnums::getTestEndPoint() : RequestEnums::getProdEndpoint();
        }

        $options = new RequestOptions('post', $type, $url);

        $request = new Request($data, $options);

        try {
            $response = $this->client->executeRequest($request);
        } catch (ClientException $e) {
            $this->logger->debug(
                'Payolution Client Log Error Request',
                [
                    'uri' => $url,
                    'request' => LogMessageFactory::createFromRequest($request->getPayload(), true),
                    'exception' => $e
                ]
            );
            return [];
        }

        if ($type === RequestEnums::CI_TYPE) {
            $xmlData = @simplexml_load_string($response->getData());
            $return = $xmlData ? json_decode(json_encode((array) $xmlData), true) : [];
        } else {
            $return = [];
            parse_str($response->getData(), $return);
        }

        $this->logger->debug(
            'Payolution Client Log Request/Response',
            [
                'uri' => $url,
                'request' => LogMessageFactory::createFromRequest($request->getPayload(), true),
                'response' => LogMessageFactory::createFromResponse($return, true)
            ]
        );

        return $return;
    }
}
