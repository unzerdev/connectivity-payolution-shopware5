<?php

namespace Payolution\DependecyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class StatusHandlerPass
 *
 * @package Payolution\DependecyInjection
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SaveHandlerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $handler = $container->getDefinition('pol_payment_payolution.payment.save_payment_handler');
        foreach ($this->getSortedTaggedServices('payol.save_handler', $container) as $service) {
            $handler->addMethodCall('addSaveHandler', [new Reference($service)]);
        }
    }

    /**
     * Get All Tagged services, sorted by priority
     *
     * @param  string $tag
     * @return Reference[]
     */
    private function getSortedTaggedServices($tag, ContainerBuilder $container)
    {
        /**
         * @var Reference[] $services
         */
        $services = [];
        foreach ($container->findTaggedServiceIds($tag) as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $services[$priority][] = new Reference($id);
        }

        if (count($services) === 0) {
            return $services;
        }

        krsort($services);

        return call_user_func_array('array_merge', $services);
    }
}
