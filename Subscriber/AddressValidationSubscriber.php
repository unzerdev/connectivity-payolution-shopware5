<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Enlight_Controller_ActionEventArgs;
use Shopware_Components_Snippet_Manager;

/**
 * Class AddressValidationSubscriber
 *
 * Listens to an event when changes in account are done.
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class AddressValidationSubscriber implements SubscriberInterface
{
    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $dbAdapter;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * AccountSubscriber constructor.
     *
     * @param Enlight_Components_Db_Adapter_Pdo_Mysql $dbAdapter
     * @param Shopware_Components_Snippet_Manager $snippetManager
     */
    public function __construct(
        Enlight_Components_Db_Adapter_Pdo_Mysql $dbAdapter,
        Shopware_Components_Snippet_Manager $snippetManager
    ) {
        $this->dbAdapter = $dbAdapter;
        $this->snippetManager = $snippetManager;
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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'validateAddress'
        ];
    }

    /**
     * Event function to run when event is fired.
     *
     * @param Enlight_Controller_ActionEventArgs $args
     * @return void
     */
    public function validateAddress(Enlight_Controller_ActionEventArgs $args)
    {
        $subject = $args->getSubject();
        $request = $subject->Request();

        if ($request->getActionName() !== 'billing') {
            return;
        }

        $view = $subject->View();

        if ($request->getParam('error') === 'street') {
            $paymentId = $request->getParam('payment');

            if (!empty($paymentId)) {
                $paymentName = $this->dbAdapter->fetchRow(
                    'SELECT description, name FROM s_core_paymentmeans WHERE id = :paymentId',
                    array(
                        ':paymentId' => $paymentId,
                    )
                );
                $messages[] = $this->snippetManager->getNamespace('frontend/pol_payment_payolution/account')->get(
                    'payolutionErrorStreetNumber_'.$paymentName['name'],
                    'Für die Nutzung von '.$paymentName['description'].' benötigen wir korrekte Adressdaten.
                    Bitte stellen Sie sicher, dass Sie eine korrekte Straße und Hausnummer eingegeben haben
                    und wählen Sie anschließen erneut '.$paymentName['description'].'.',
                    true
                );
            } else {
                $messages[] = $this->snippetManager->getNamespace('frontend/pol_payment_payolution/account')->get(
                    'payolutionErrorStreetNumber',
                    'Geben Sie bitte Ihre Hausnummer ein.',
                    true
                );
            }

            $view->assign('sErrorMessages', $messages);
        }
    }
}