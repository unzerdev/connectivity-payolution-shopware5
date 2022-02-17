<?php

namespace Payolution\DependecyInjection;

use Payolution\Client\PayolutionClient57;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Config pass change services dynamical
 *
 * @package Payolution\DependecyInjection
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class ShopwareCompatibilityPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $shopwareVersion;

    /**
     * ConfigErrorLevelActivationPass constructor.
     *
     * @param string $shopwareVersion
     */
    public function __construct(string $shopwareVersion)
    {
        $this->shopwareVersion = $shopwareVersion;
    }

    /**
     * Build the config log strategy based on the shopware version
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $clientDefinition = $container->getDefinition('payolution.client.payolution_client');

        if (version_compare($this->shopwareVersion, '5.7', '>=')) {
            $clientDefinition->setClass(PayolutionClient57::class);
        }
    }
}
