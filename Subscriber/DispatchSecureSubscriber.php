<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Controller_ActionEventArgs;
use Enlight_Template_Manager;
use Exception;
use Payolution\Config\ConfigLoader;
use PolPaymentPayolution\ComponentManager\ComponentManager;
use PolPaymentPayolution\Util\Session\SessionManager;

/**
 * Class DispatchSecureSubscriber
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 *
 */
class DispatchSecureSubscriber implements SubscriberInterface
{

    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @var Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var ComponentManager
     */
    private $componentManager;

    /**
     * DispatchSecureSubscriber constructor.
     *
     * @param string $pluginDirectory
     * @param SessionManager $sessionManager
     * @param ComponentManager $componentManager
     * @param Enlight_Template_Manager $templateManager
     * @param ConfigLoader $configLoader
     */
    public function __construct(
        $pluginDirectory,
        SessionManager $sessionManager,
        ComponentManager $componentManager,
        Enlight_Template_Manager $templateManager,
        ConfigLoader $configLoader
    ) {
        $this->pluginDirectory = $pluginDirectory;
        $this->sessionManager = $sessionManager;
        $this->componentManager = $componentManager;
        $this->templateManager = $templateManager;
        $this->configLoader = $configLoader;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @inheritdoc
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure' => 'onActionPostDispatchSecure'
        ];
    }

    /**
     * Event function, adds some variables to the templates.
     *
     * @param Enlight_Controller_ActionEventArgs $args
     * @return void
     * @throws Exception
     */
    public function onActionPostDispatchSecure(Enlight_Controller_ActionEventArgs $args)
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $request = $controller->Request();

        if ($request->getModuleName() !== 'frontend') {
            return;
        }

        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        $currentControllerAction = sprintf('%s/%s', $controllerName, $actionName);

        if ($this->sessionManager->get('currentAction') !== $currentControllerAction) {
            $this->sessionManager->set('lastAction', $this->sessionManager->get('currentAction'));
            $this->sessionManager->set('currentAction', $currentControllerAction);
        }

        $basketAmount = $this->componentManager->getBasketModule()->sGetAmount();
        if ($currentControllerAction === 'checkout/finish') {
            $this->sessionManager->set('payolutionOrderDone', 1);
        } elseif ($currentControllerAction === 'checkout/confirm' && !empty($basketAmount)) {
            $this->sessionManager->set('payolutionOrderDone', 0);
        }

        $view = $controller->View();

        $this->templateManager->addTemplateDir($this->pluginDirectory.'/Resources/views');

        if ($controllerName === 'checkout') {
            $locale = $this->configLoader->getCurrentShop()->getLocale()->getLocale();
            $locale = explode('_', $locale);

            $view->assign('payolutionLocale', strtolower($locale[0]));
        }

        if (in_array($currentControllerAction, array('checkout/shippingPayment', 'account/payment'))) {
            $view->assign('payolutionControllerCheck', $controllerName);
        } else {
            $view->assign('payolutionControllerCheck', false);
        }
    }
}
