<?php
namespace PolPaymentPayolution;

use Payolution\Config\AbstractConfig;
use Psr\Log\LoggerInterface;

/**
 * Class Log
 *
 * @package PolPaymentPayolution
 */
class Log
{
    /**
     * @var AbstractConfig
     */
    private $payolutionConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Log constructor.
     *
     * @param AbstractConfig $payolutionConfig
     * @param LoggerInterface $logger
     */
    public function __construct(AbstractConfig $payolutionConfig, LoggerInterface $logger)
    {
        $this->payolutionConfig = $payolutionConfig;
        $this->logger = $logger;
    }

    /**
     * set Logging
     *
     * @param array $data
     */
    public function log(array $data)
    {
        if($this->payolutionConfig->isLogging()) {
            foreach($data as $key => $value) {
                $value = json_encode($value);
                $this->logger->debug('Payolution:['.$key.'] ' . $value);
            }
        }
    }
}
