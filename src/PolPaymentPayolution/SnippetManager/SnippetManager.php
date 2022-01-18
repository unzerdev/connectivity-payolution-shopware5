<?php

namespace PolPaymentPayolution\SnippetManager;

use Shopware_Components_Snippet_Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SnippetManager
 *
 * @package PolPaymentPayolution\SnippetManager
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SnippetManager implements SnippetManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SnippetManager constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get Snippet By Name
     *
     * @param string $name
     * @param string $nameSpace
     * @param string|null $default
     * @param bool $save
     *
     * @return string
     */
    public function getByName($name, $nameSpace, $default = null, $save = true)
    {
        return $this->getSnippetManager()->getNamespace($nameSpace)->get($name, $default, $save);
    }

    /**
     * Get Shopware Snippet Manager
     *
     * @return Shopware_Components_Snippet_Manager
     */
    private function getSnippetManager()
    {
        return $this->container->get('snippets');
    }
}
