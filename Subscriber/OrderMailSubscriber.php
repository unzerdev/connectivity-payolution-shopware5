<?php

namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Mail;
use Enlight_Event_EventArgs;
use Payolution\Config\AbstractConfig;

/**
 * Decorate order mails
 *
 * @package PolPaymentPayolution\Subscriber
 */
class OrderMailSubscriber implements SubscriberInterface
{
    /**
     * The payolution config
     *
     * @var AbstractConfig
     */
    private $payolutionConfig;

    /**
     * OrderMailSubscriber constructor.
     *
     * @param AbstractConfig $payolutionConfig
     */
    public function __construct(AbstractConfig $payolutionConfig)
    {
        $this->payolutionConfig = $payolutionConfig;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Order_SendMail_Filter' => 'addBcc'
        ];
    }

    /**
     * Adds a bcc recipient to an email.
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return Enlight_Components_Mail
     */
    public function addBcc(Enlight_Event_EventArgs $args)
    {
        $context = $args->get('context');

        /** @var Enlight_Components_Mail $mail */
        $mail = $args->getReturn();

        if ($context['additional']['payment']['action'] === 'PolPaymentPayolution') {
            $bccMail = $this->payolutionConfig->getMailBcc();

            if(!empty($bccMail)) {
                $mail->addBcc($bccMail);
            }
        }

        $args->setReturn($mail);

        return $mail;
    }
}
