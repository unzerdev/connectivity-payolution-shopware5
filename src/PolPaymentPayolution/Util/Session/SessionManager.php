<?php

namespace PolPaymentPayolution\Util\Session;

use Enlight_Components_Session_Namespace;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SessionManager
 *
 * @package PolPaymentPayolution\Util\Session
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SessionManager implements SessionManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SessionManager constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get($key, $defaultValue = null)
    {
        return $this->getSession()->get($key, $defaultValue);
    }

    /**
     * Get shopware session
     *
     * @return Enlight_Components_Session_Namespace
     */
    private function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        $this->getSession()->offsetSet($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return $this->getSession()->offsetExists($key);
    }

    /**
     * @inheritDoc
     */
    public function remove($key)
    {
        $this->getSession()->offsetUnset($key);
    }
}
