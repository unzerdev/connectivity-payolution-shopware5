<?php

namespace PolPaymentPayolution\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Psr\Log\LoggerInterface;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowElement;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowHistory;

/**
 * Class EntityManager
 *
 * @package PolPaymentPayolution\Doctrine
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class EntityManagerWrapper
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $entityWhiteList = [
        WorkflowElement::class,
        WorkflowHistory::class
    ];

    /**
     * EntityManagerWrapper constructor.
     *
     * @param ComponentManagerInterface $component
     * @param LoggerInterface $logger
     */
    public function __construct(ComponentManagerInterface $component, LoggerInterface $logger)
    {
        $this->entityManager = $component->getModelManager();
        $this->logger = $logger;
    }

    /**
     * Persist Entity
     *
     * @param object $entity
     *
     * @return void
     */
    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }

    /**
     * Flush Entity
     *
     * @param object|null $entity
     *
     * @return void
     */
    public function flush($entity = null)
    {
        if ($entity && UnitOfWork::STATE_NEW === $this->getStateOfEntity($entity)
        ) {
            $this->persist($entity);
        }

        if ($this->checkFlush()) {
            $this->entityManager->flush($entity);

            return;
        }

        $this->logger->debug('Compute ChangeSet for EM new');

        $unitOfWork = $this->entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();
    }

    /**
     * Get State of Entity
     *
     * @param object $entity
     *
     * @return int
     */
    private function getStateOfEntity($entity)
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();

        return $unitOfWork->getEntityState($entity);
    }

    /**
     * Check If Em should be flushed
     *
     * @return bool
     */
    private function checkFlush()
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();

        $check = true;
        //Check if sw models are in em pipeline for an insertion
        foreach ($unitOfWork->getScheduledEntityInsertions() as $insertion) {
            if (!in_array(get_class($insertion), $this->entityWhiteList, true)) {
                $check = false;
            }
        }

        //Check if sw models are in em pipeline for an update
        foreach ($unitOfWork->getScheduledEntityUpdates() as $update) {
            if (!in_array(get_class($update), $this->entityWhiteList, true)) {
                $check = false;
            }
        }

        //Check if sw models are in em pipeline for an delete
        foreach ($unitOfWork->getScheduledEntityDeletions() as $delete) {
            if (!in_array(get_class($delete), $this->entityWhiteList, true)) {
                $check = false;
            }
        }

        return $check;
    }
}