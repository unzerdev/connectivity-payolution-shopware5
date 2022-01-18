<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Hook_HookArgs;
use PolPaymentPayolution\RiskManagement\Context\RiskManagementContextFactory;
use PolPaymentPayolution\RiskManagement\RiskManagementExtension;

/**
 * Subscriber that adds a risk value to the response.
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class ManageRisksSubscriber implements SubscriberInterface
{
    /**
     * Context factory for risk management
     *
     * @var RiskManagementContextFactory
     */
    private $contextFactory;

    /**
     * Extension for risk mananagement
     *
     * @var RiskManagementExtension
     */
    private $extension;

    /**
     * ManageRisksSubscriber constructor.
     *
     * @param RiskManagementContextFactory $contextFactory
     * @param RiskManagementExtension $extension
     */
    public function __construct(RiskManagementContextFactory $contextFactory, RiskManagementExtension $extension)
    {
        $this->contextFactory = $contextFactory;
        $this->extension = $extension;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'sAdmin::sManageRisks::after' => 'checkRisk'
        ];
    }

    /**
     * Event function. Checks risk and adds that result to the response.
     *
     * @param Enlight_Hook_HookArgs $args
     *
     * @return bool
     */
    public function checkRisk(Enlight_Hook_HookArgs $args)
    {
        $context = $this->contextFactory->createContextFromHookArgs($args);

        $risk = $this->extension->checkRisk($context);

        $args->setReturn($risk->isRiskValue());

        return $risk->isRiskValue();
    }
}
