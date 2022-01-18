<?php

namespace Payolution\Session;

use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Models\Order\Order;
use SimpleXMLElement;

/**
 * Class SessionRequestDecorator
 *
 * @package Payolution\Session
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SessionRequestDecorator
{
    /**
     * White list for allowed payment codes with session id
     *
     * @var array
     */
    private $sessionWhiteList = [
        'VA.PA',
        'VA.PC'
    ];

    /**
     * @var SessionTokenStorage
     */
    private $sessionTokenStorage;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SessionRequestDecorator constructor.
     *
     * @param SessionTokenStorage $sessionTokenStorage
     * @param ComponentManagerInterface $componentManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        SessionTokenStorage $sessionTokenStorage,
        ComponentManagerInterface $componentManager,
        LoggerInterface $logger
    ) {
        $this->sessionTokenStorage = $sessionTokenStorage;
        $this->componentManager = $componentManager;
        $this->logger = $logger;
    }

    /**
     * Append Session CI
     *
     * @param array $xml
     *
     * @return array
     */
    public function appendSessionCI(array $xml)
    {
        $this->logger->debug('AppendSessionCI');

        $simpleXml = new SimpleXMLElement($xml['body']['payload']);

        $this->logger->info('Read session from cookie');
        $token = $this->sessionTokenStorage->getToken();

        $this->logger->info('Token' . $token);
        if ($token) {
            $transAction = $simpleXml->xpath('/Request/Transaction')[0];
            if (!isset($transAction[0])) {
                return $xml;
            }
            $analysisResult = $simpleXml->xpath('/Request/Transaction/$analysis');
            if (isset($analysisResult[0])) {
                $analysis = $analysisResult[0];
            } else {
                $analysis = $transAction->addChild('Analysis');

            }

            $criterion = $analysis->addChild('Criterion', $token);
            $criterion->addAttribute('name', 'PAYOLUTION_SESSION_ID');
            $xml['body']['payload'] = $simpleXml->saveXML();
        }

        return $xml;
    }

    /**
     * Append Session to request
     *
     * @param array $payload
     *
     * @return array
     */
    public function appendSession(array $payload)
    {
        $this->logger->debug('AppendSession');

        if (!isset($payload['body'], $payload['body'], $payload['body']['PAYMENT.CODE'])) {
            return $payload;
        }

        if (!in_array($payload['body']['PAYMENT.CODE'], $this->sessionWhiteList, true)) {
            return $payload;
        }

        $token = null;
        if (!$orderId = $this->extractOrderIdFromPayload($payload['body'])) {
            $this->logger->info('Read session from cookie');
            $token = $this->sessionTokenStorage->getToken();
        } else {
            $this->logger->info('Read session from order');
            $token = $this->sessionTokenStorage->getToken($orderId);
        }

        if ($token) {
            $this->logger->info('Token: ' . $token);
            $payload['body']['CRITERION.PAYOLUTION_SESSION_ID'] = $this->sessionTokenStorage->getToken(
                $orderId
            );
        }

        return $payload;
    }

    /**
     * Extract Order ID From Payload
     *
     * @param $payload
     * @return null|int
     */
    private function extractOrderIdFromPayload($payload)
    {
        if (!isset($payload['IDENTIFICATION.TRANSACTIONID'])) {
            return null;
        }

        $order = $this->componentManager->getModelManager()->getRepository(Order::class)
            ->findOneBy(
                [
                'number' => $payload['IDENTIFICATION.TRANSACTIONID']
                ]
            );

        if (!$order) {
            return null;
        }

        return $order->getId();
    }
}
