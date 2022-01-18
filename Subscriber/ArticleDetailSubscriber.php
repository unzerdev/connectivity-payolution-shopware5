<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use PolPaymentPayolution\Installment\InstallmentVoter;
use PolPaymentPayolution\Payment\PaymentProvider;

/**
 * Class ArticleDetailSubscriber
 *
 * Listens to the event Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ArticleDetailSubscriber implements SubscriberInterface
{
    /**
     * @var InstallmentVoter
     */
    private $installmentVoter;

    /**
     * @var PaymentProvider
     */
    private $paymentProvider;

    /**
     * DetailSubscriber constructor.
     *
     * @param InstallmentVoter $installmentVoter
     * @param PaymentProvider $paymentProvider
     */
    public function __construct(InstallmentVoter $installmentVoter, PaymentProvider $paymentProvider)
    {
        $this->installmentVoter = $installmentVoter;
        $this->paymentProvider = $paymentProvider;
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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'addTemplateVariables'
        ];
    }

    /**
     * Add template variables
     *
     * @param Enlight_Controller_ActionEventArgs $args
     *
     * @return  void
     */
    public function addTemplateVariables(Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();

        if ($article = $view->getAssign('sArticle')) {
            $view->assign(
                'payolutionInstallmentActive',
                $this->installmentVoter->vote($article)
            );

            $view->assign(
                'payolutionCurrency',
                $this->paymentProvider->getCurrentCurrency()
            );
        }
    }
}
