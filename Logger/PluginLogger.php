<?php declare(strict_types = 1);


namespace PolPaymentPayolution\Logger;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Payolution\Config\ConfigLoader;
use PolPaymentPayolution\Log;
use Psr\Log\LoggerInterface;

class PluginLogger implements LoggerInterface
{
    /** @var Logger */
    private $logger;

    /** @var null|bool */
    private $isLogging;

    public function __construct(Logger $logger, ConfigLoader $configLoader)
    {
        $this->logger       = $logger;
        $this->isLogging    = $configLoader->loadCurrentConfig()->isLogging();
    }

    public function addRecord(int $level, string $message, array $context = []): bool
    {
        if ($level < Logger::ERROR && !$this->isLogging) {
            return false;
        }

        return $this->logger->addRecord($level, $message, $context);
    }

    public function emergency($message, array $context = []): void
    {
        $this->addRecord(Logger::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->addRecord(Logger::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->addRecord(Logger::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->addRecord(Logger::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->addRecord(Logger::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->addRecord(Logger::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->addRecord(Logger::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->addRecord(Logger::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->addRecord($level, $message, $context);
    }

}
