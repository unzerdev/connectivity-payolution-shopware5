<?php

namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Shopware_Components_Cron_CronJob as Cronjob;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Subscriber to handle all payolution cronjobs
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class CronjobSubscriber implements LoggerAwareInterface, SubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * The filesystem wrapper
     *
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * The shopware doc path
     *
     * @var string
     */
    private $shopwareDocPath;

    /**
     * CronjobSubscriber constructor.
     *
     * @param Filesystem $fileSystem
     * @param string $shopwareDocPath
     */
    public function __construct(Filesystem $fileSystem, $shopwareDocPath)
    {
        $this->fileSystem = $fileSystem;
        $this->shopwareDocPath = $shopwareDocPath;

        $this->logger = new NullLogger();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @inheritdoc
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_DeletePayolutionLibrary' => 'onRun'
        ];
    }

    /**
     * Run delete js file cronjob
     *
     * @param Cronjob $job
     */
    public function onRun(Cronjob $job)
    {
        $result = true;
        $file = $this->shopwareDocPath . 'files/payolution/jsClLibrary.js';
        $this->logger->info('Delete payolution jsClLibrary.js file', [
            'jobName' => $job->getName(),
            'file' => $file
        ]);

        try {
            $this->fileSystem->remove($this->shopwareDocPath . 'files/payolution/jsClLibrary.js');
        } catch (IOException $e) {
            $this->logger->error('Error at deleting jsClLibrary.js file', ['exception' => $e]);
            $result = false;
        }

        $job->setReturn($result);
    }
}