<?php

namespace PolPaymentPayolution\RiskManagement\Context;

use Enlight_Hook_HookArgs as HookArgs;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Config\Config;
use PolPaymentPayolution\Config\ConfigProvider;
use PolPaymentPayolution\Exception\RiskSkipException;
use PolPaymentPayolution\Util\Session\SessionManagerInterface;
use RuntimeException;

/**
 * Class RiskManagementContextFactory
 *
 * @package PolPaymentPayolution\RiskManagement\Context
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class RiskManagementContextFactory
{
    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * RiskManagementContextFactory constructor.
     *
     * @param SessionManagerInterface $session
     * @param ComponentManagerInterface $componentManager
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        SessionManagerInterface $session,
        ComponentManagerInterface $componentManager,
        ConfigProvider $configProvider
    ) {
        $this->session = $session;
        $this->componentManager = $componentManager;
        $this->configProvider = $configProvider;
    }

    /**
     * Create Context
     *
     * @param array $basket
     * @param array $user
     * @param int $paymentId
     *
     * @return RiskManagementContext
     */
    public function createContext(array $basket, array $user, $paymentId)
    {
        $paymentName = $this->getPaymentName($paymentId);

        return new RiskManagementContext(
            $this->getConfig(),
            false,
            $paymentId,
            $basket,
            $user,
            $paymentName,
            $this->getPaymentShortName($paymentName)
        );
    }

    /**
     * Creates a risk management context from Enlight_Hook_HookArgs.
     *
     * @param HookArgs $hookArgs
     *
     * @return RiskManagementContext*
     */
    public function createContextFromHookArgs(HookArgs $hookArgs)
    {
        if ($hookArgs->getName() !== 'sAdmin::sManageRisks::after') {
            throw new RuntimeException('Context can only be created for `sAdmin::sManageRisks::after` hook');
        }

        $args = $hookArgs->getArgs();
        $basket = empty($args[1]) ? [] : $args[1];
        $paymentId = $args[0];
        $user = empty($args[2]) ? [] : $args[2];

        if (empty($basket)) {
            $basket = [
                'content' => $this->session->get('sBasketQuantity'),
                'AmountNumeric' => $this->session->get('sBasketAmount'),
            ];
        }

        $paymentName = $this->getPaymentName($paymentId);

        return new RiskManagementContext(
            $this->getConfig(),
            $hookArgs->getReturn(),
            $paymentId,
            $basket,
            $user,
            $paymentName,
            $this->getPaymentShortName($paymentName)
        );
    }

    /**
     * Get PaymentName
     *
     * @param int $paymentId
     *
     * @return string
     */
    private function getPaymentName($paymentId)
    {
        return $this->componentManager->getDatabase()->fetchOne(
            'SELECT
              name
            FROM
              s_core_paymentmeans
            WHERE
              id = :paymentId',
            array(
                ':paymentId' => $paymentId
            )
        );
    }

    /**
     * Get Payment short name
     *
     * @param string $paymentName
     * @return string
     */
    private function getPaymentShortName($paymentName)
    {
        $shortName = $paymentName;
        switch ($paymentName) {
            case 'payolution_invoice_b2c':
                $shortName =  'PAYOLUTION_INVOICE';
                break;
            case 'payolution_invoice_b2b':
                $shortName = 'PAYOLUTION_INVOICE_B2B';
                break;
            case 'payolution_installment':
                $shortName = 'PAYOLUTION_INS';
                break;
            case 'payolution_elv':
                $shortName = 'PAYOLUTION_ELV';
                break;
        }

        return $shortName;
    }

    /**
     * Get the config for the current shop
     *
     * @return Config
     */
    private function getConfig()
    {
        return $this->configProvider->getConfig();
    }
}
