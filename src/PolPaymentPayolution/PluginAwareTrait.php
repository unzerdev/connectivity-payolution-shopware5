<?php


namespace PolPaymentPayolution;

use PolPaymentPayolution\Legacy\BootstrapWrapper;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Trait PluginAwareTrait
 *
 * @package PolPaymentPayolution
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 *
 * @deprecated Use the values form the dic
 */
trait PluginAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Get Plugin
     *
     * @return BootstrapWrapper
     */
    public function getPlugin()
    {
        return $this->container->get('pol_payment_payolution.legacy.bootstrap_wrapper');
    }
}
