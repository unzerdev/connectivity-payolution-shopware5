<?php

namespace PolPaymentPayolution\Repository;

use Doctrine\ORM\EntityRepository;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;

/**
 * Class RepositoryManager
 *
 * @package PolPaymentPayolution\Repository
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RepositoryManager
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * RepositoryManager constructor.
     *
     * @param ComponentManagerInterface $componentManager
     */
    public function __construct(ComponentManagerInterface $componentManager)
    {
        $this->componentManager = $componentManager;
    }

    /**
     * Get Repository By Class name
     *
     * @param string $class
     *
     * @return EntityRepository
     */
    public function getRepository($class)
    {
        $entityManager = $this->componentManager->getModelManager();

        return $entityManager->getRepository($class);
    }
}