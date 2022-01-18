<?php

namespace PolPaymentPayolution\State;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\Enum\PaymentType;
use Shopware\Models\Order\Order;

/**
 * Class OrderStateHandler
 *
 * @package PolPaymentPayolution\State
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class OrderStateHandler
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * The plugin name
     *
     * @var string
     */
    private $pluginName;

    /**
     * The plugin version
     *
     * @var string
     */
    private $pluginVersion;

    /**
     * OrderStateHandler constructor.
     *
     * @param ComponentManagerInterface $componentManager
     * @param string $pluginName
     * @param string $pluginVersion
     */
    public function __construct(ComponentManagerInterface $componentManager, string $pluginName, string $pluginVersion)
    {
        $this->componentManager = $componentManager;
        $this->pluginName = $pluginName;
        $this->pluginVersion = $pluginVersion;
    }

    /**
     * Set States
     *
     * @param int $orderId
     * @param PluginConfig $config
     * @param string $type
     *
     * @return void
     *
     * @throws Exception
     */
    public function setState($orderId, PluginConfig $config, $type)
    {
        $payolutionMode = $this->getPayolutionMode($orderId);

        if ($type === PaymentType::CAPTURE) {
            $this->setCaptureState($payolutionMode, $config, $orderId);
        } elseif ($type === PaymentType::REFUND || $type === PaymentType::REVERSAL) {
            $this->setRefundState($payolutionMode, $config, $orderId);
        }
    }

    /**
     * Set Capture State
     *
     * @param string $payolutionMode
     * @param PluginConfig $pluginConfig
     * @param int $orderId
     *
     * @return void
     *
     * @throws Exception
     */
    private function setCaptureState($payolutionMode, PluginConfig $pluginConfig, $orderId)
    {
        $oderModule = $this->componentManager->getOrderModule();
        $orderStateId = 21;
        switch ($payolutionMode) {
            case PaymentType::B2C_PAYMENT:
                $orderStateId = $pluginConfig->getCaptureOrderStateB2C();
                break;
            case PaymentType::B2B_PAYMENT:
                $orderStateId = $pluginConfig->getCaptureOrderStateB2B();
                break;
            case PaymentType::ELV_PAYMENT:
                $orderStateId = $pluginConfig->getCaptureOrderStateELV();
                break;
            case PaymentType::INSTALLMENT_PAYMENT:
                $orderStateId = $pluginConfig->getOrderStateInstallment();
                break;
        }

        $oderModule->setPaymentStatus(
            $orderId,
            $orderStateId,
            false,
            sprintf(
                'Status Change By Plugin %s in Version %s',
                $this->pluginName,
                $this->pluginVersion
            )
        );
    }

    /**
     * Set refund States
     *
     * @param string $payolutionMode
     * @param PluginConfig $pluginConfig
     * @param int $orderId
     *
     * @return void
     */
    private function setRefundState($payolutionMode, PluginConfig $pluginConfig, $orderId)
    {
        $oderModule = $this->componentManager->getOrderModule();
        $orderStateId = 21;
        switch ($payolutionMode) {
            case PaymentType::B2C_PAYMENT:
                $orderStateId = $pluginConfig->getRefundOrderStateB2C();
                break;
            case PaymentType::B2B_PAYMENT:
                $orderStateId = $pluginConfig->getRefundOrderStateB2B();
                break;
            case PaymentType::ELV_PAYMENT:
                $orderStateId = $pluginConfig->getRefundOrderStateELV();
                break;
            case PaymentType::INSTALLMENT_PAYMENT:
                $orderStateId = $pluginConfig->getRefundOrderStateInstallment();
                break;
        }

        $oderModule->setPaymentStatus(
            $orderId,
            $orderStateId,
            false,
            sprintf(
                'Status Change By Plugin %s in Version %s',
                $this->pluginName,
                $this->pluginVersion
            )
        );
    }

    /**
     * Get Payolution Mode
     *
     * @param int $orderId
     *
     * @return string
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function getPayolutionMode($orderId)
    {
        /** @var Order $order */
        $order = $this->componentManager->getModelManager()->find(Order::class, $orderId);

        return $order->getPayment()->getName();
    }
}
