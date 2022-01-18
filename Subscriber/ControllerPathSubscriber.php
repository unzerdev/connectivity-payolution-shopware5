<?php

namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;

/**
 * Subscriber to add all controllers
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ControllerPathSubscriber implements SubscriberInterface
{
    /**
     * @var Enlight_Template_Manager
     */
    private $template;

    /**
     * The folder where the backend controllers are
     *
     * @var string
     */
    private $controllerFolderBackend;

    /**
     * The folder where the frontend controllers are
     *
     * @var string
     */
    private $controllerFolderFrontend;

    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * ControllerPathSubscriber constructor.
     *
     * @param string $controllerFolderBackend
     * @param string $controllerFolderFrontend
     * @param string $pluginDirectory
     */
    public function __construct(Enlight_Template_Manager $template, $controllerFolderBackend, $controllerFolderFrontend, $pluginDirectory)
    {
        $this->template = $template;
        $this->controllerFolderBackend = $controllerFolderBackend;
        $this->controllerFolderFrontend = $controllerFolderFrontend;
        $this->pluginDirectory = $pluginDirectory;
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
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_PolPaymentPayolution' => 'onPolPaymentPayolution',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_PayolutionWorkflow' => 'onPayolutionWorkflow',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_PayolutionCapture' => 'onPayolutionCapture',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_PayolutionRefund' => 'onPayolutionRefund',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_PayolutionConfig' => 'onPayolutionConfig',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_PayolutionLog' => 'onPayolutionLog',
        ];
    }

    /**
     * Add frontend controller
     *
     * @return string The controller file
     */
    public function onPolPaymentPayolution()
    {
        $this->template->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        return  $this->controllerFolderFrontend .  DIRECTORY_SEPARATOR . 'PolPaymentPayolution.php';
    }

    /**
     * Add backend controller
     *
     * @return string The controller file
     */
    public function onPayolutionWorkflow()
    {
        $this->template->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        return $this->controllerFolderBackend . DIRECTORY_SEPARATOR . 'PayolutionWorkflow.php';
    }

    /**
     * Add backend controller
     **
     * @return string The controller file
     */
    public function onPayolutionCapture()
    {
        $this->template->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        return $this->controllerFolderBackend . DIRECTORY_SEPARATOR . 'PayolutionCapture.php';
    }

    /**
     * Add backend controller
     **
     * @return string The controller file
     */
    public function onPayolutionRefund()
    {
        $this->template->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        return $this->controllerFolderBackend . DIRECTORY_SEPARATOR . 'PayolutionRefund.php';
    }

    /**
     * Add backend controller
     **
     * @return string The controller file
     */
    public function onPayolutionConfig()
    {
        $this->template->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        return $this->controllerFolderBackend . DIRECTORY_SEPARATOR . 'PayolutionConfig.php';
    }

    /**
     * Add backend controller
     **
     * @return string The controller file
     */
    public function onPayolutionLog()
    {
        $this->template->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        return $this->controllerFolderBackend . DIRECTORY_SEPARATOR . 'PayolutionLog.php';
    }
}
