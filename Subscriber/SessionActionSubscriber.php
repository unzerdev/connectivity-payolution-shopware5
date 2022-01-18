<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Controller_Request_Request;
use Enlight_Event_EventArgs;
use PolPaymentPayolution\Util\Session\SessionManager;

/**
 * Class SessionActionSubscriber
 *
 * Subscriber which sets some session variables during checkout.
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class SessionActionSubscriber implements SubscriberInterface
{
    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * PreDispatchSubscriber constructor.
     *
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
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
            'sOrder::Enlight_Controller_Action_PreDispatch::before' => 'onUpdateSession'
        ];
    }

    /**
     * Event function, sets some variables in the session.
     *
     * @param Enlight_Event_EventArgs $args
     * @return void
     */
    public function onUpdateSession(Enlight_Event_EventArgs $args)
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');

        /** @var Enlight_Controller_Request_Request $request */
        $request = $controller->Request();

        if (0 !== strcmp('frontend', $request->getModuleName())) {
            return;
        }

        $actionName = $request->getActionName();

        if ($actionName === 'confirm') {
            $this->sessionManager->set('payolutionControllerAction', 'confirm');
            $this->sessionManager->set('payolutionInstallmentArray', '');

            return;
        }

        $controllerNames = array('detail', 'PolPaymentPayolution', 'checkout');
        if (in_array($request->getControllerName(), $controllerNames) === false) {
            $this->sessionManager->set('payolutionInstallmentArray', '');
        }

        $this->sessionManager->set('payolutionControllerAction', '');
    }
}
