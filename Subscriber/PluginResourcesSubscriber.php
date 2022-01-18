<?php

namespace PolPaymentPayolution\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Enlight_Template_Manager;
use Enlight_View_Default;
use PolPaymentPayolution\Smarty\SessionTokenFunction;
use Shopware\Components\Theme\LessDefinition;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;

/**
 * Subscriber to handle all plugin resources
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PluginResourcesSubscriber implements SubscriberInterface
{
    /**
     * @var Enlight_Template_Manager
     */
    private $template;

    /**
     * @var SessionTokenFunction
     */
    private $smartySessionTokenFunction;

    /**
     * The base dir for the plugin
     *
     * @var string
     */
    private $pluginDirectory;

    /**
     * PluginResourcesSubscriber constructor.
     *
     * @param Enlight_Template_Manager $template
     * @param SessionTokenFunction $smartySessionTokenFunction
     * @param string $pluginDirectory
     */
    public function __construct(
        Enlight_Template_Manager $template,
        SessionTokenFunction $smartySessionTokenFunction,
        $pluginDirectory
    ) {
        $this->template = $template;
        $this->smartySessionTokenFunction = $smartySessionTokenFunction;
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
            'Enlight_Controller_Action_PostDispatchSecure' => 'onActionPostDispatchSecure',
            'Enlight_Controller_Action_PostDispatch' => 'onActionPostDispatchSecure',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => 'onLoadBackendIndex',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Order' => 'onPostDispatchOrder',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Payment' => 'onPostDispatchBackendPayment',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascript',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLessFiles',
            'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDir',
        ];
    }

    /**
     * On frontend page register smarty plugins
     *
     * @return void
     */
    public function onActionPostDispatchSecure(ActionEventArgs $args)
    {
        // Only register plugin if the plugin is not already registered this avoids an fatal smarty error
        if (!isset($this->template->smarty->registered_plugins['function']['payolutionsessiontoken'])) {
            $this->template->smarty->registerPlugin(
                'function',
                'payolutionsessiontoken',
                [
                    $this->smartySessionTokenFunction,
                    'getToken'
                ]
            );
        }

        $subject = $args->getSubject();
        $view = $subject->View();

        if ($view->getAssign('payolutionIncludeFraudPrevention')) {
            $view->assign('sessionToken', $this->smartySessionTokenFunction->getToken());
        }
    }

    /**
     * Add the template dir and extJS templates on backend config
     *
     * @param ActionEventArgs $args
     *
     * @return void
     */
    public function onPostDispatchBackendPayment(ActionEventArgs $args)
    {
        /* @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/pol_payment_payolution/payment/view/main/window.js');
            $view->extendsTemplate('backend/pol_payment_payolution/payment/controller/payment.js');
        }

        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/pol_payment_payolution/payment/app.js');
        }
    }

    /**
     * Add the template dir on backend index
     *
     * @param ActionEventArgs $args
     *
     * @return void
     */
    public function onLoadBackendIndex(ActionEventArgs $args)
    {
        /** @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');
    }

    /**
     * Add the template dir and extJS templates on backend config
     *
     * @param ActionEventArgs $args
     *
     * @return void
     */
    public function onPostDispatchConfig(ActionEventArgs $args)
    {
        /* @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/pol_payment_payolution/config/view/form/document.js');
        }
    }

    /**
     * Add the template dir and extJS templates on backend order
     *
     * @param ActionEventArgs $args
     *
     * @return void
     */
    public function onPostDispatchOrder(ActionEventArgs $args)
    {
        /* @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');

        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/pol_payment_payolution/order/view/detail/window.js');
            $view->extendsTemplate('backend/pol_payment_payolution/order/controller/main.js');
        }

        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/pol_payment_payolution/order/app.js');
        }
    }

    /**
     * Add template dirs for the theme
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return void
     */
    public function onCollectTemplateDir(Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();
        $dirs[] = $this->pluginDirectory . '/Resources/views/';
        $args->setReturn($dirs);
    }
    
    /**
     * Will return an ArrayCollection object of all less files that the plugin provides.
     *
     * @return ArrayCollection
     */
    public function onCollectLessFiles()
    {
        $less = new LessDefinition(
            [],
            [$this->pluginDirectory . '/Resources/views/frontend/_public/src/less/all.less'],
            $this->pluginDirectory
        );
        return new ArrayCollection([$less]);
    }

    /**
     * Will return an ArrayCollection object of all js files that the plugin provides.
     *
     * @return ArrayCollection
     */
    public function onCollectJavascript()
    {
        $jsPaths = [
            $this->pluginDirectory . '/Resources/views/frontend/_public/src/js/payolution_payment.js',
            $this->pluginDirectory . '/Resources/views/frontend/_public/src/js/payolution_installment.js',
        ];
        return new ArrayCollection($jsPaths);
    }
}