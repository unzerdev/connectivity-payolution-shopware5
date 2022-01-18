<?php

namespace PolPaymentPayolution\Logger;

use Monolog\Handler\FingersCrossed\ActivationStrategyInterface;
use Monolog\Logger;
use Payolution\Config\ConfigLoader;

/**
 * Custom activation strategy to log only errors or all logs if configured
 *
 * @package PolPaymentPayolution\Logger
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ConfigErrorLevelActivationStrategy implements ActivationStrategyInterface
{
    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * Cache for the logging active flag
     *
     * @var bool|null
     */
    private $isLoggingActiveCache;

    /**
     * ConfigErrorLevelActivationStrategy constructor.
     *
     * @param ConfigLoader $configLoader
     */
    public function __construct(ConfigLoader $configLoader)
    {
        $this->configLoader = $configLoader;
    }

    /**
     * Returns whether the given record activates the handler.
     *
     * @param  array $record
     *
     * @return Boolean
     */
    public function isHandlerActivated(array $record)
    {
        if ($this->isLoggingActiveCache === null) {
            $this->isLoggingActiveCache = $this->configLoader->loadCurrentConfig()->isLogging();
        }

        return ($record['level'] >= Logger::ERROR) || $this->isLoggingActiveCache;
    }
}