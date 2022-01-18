<?php

namespace PayolutionPickware\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use PayolutionPickware\Position\OrderModelHandler;
use Shopware\Models\Attribute\OrderDetail;
use Shopware\Models\Order\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PickwareModelSubscriber
 *
 * @package PayolutionPickware\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PickwareOrderModelSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var OrderModelHandler
     */
    private $orderModelHandler;

    /**
     * Is the pickware plugin active?
     *
     * @var bool
     */
    private $pickwareActive;

    /**
     * PickwareOrderModelSubscriber constructor.
     *
     * @param ContainerInterface $container
     * @param bool $pickwareActive
     */
    public function __construct(ContainerInterface $container, $pickwareActive)
    {
        $this->container = $container;
        $this->pickwareActive = $pickwareActive;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        $events = [];

        // Only activate events if oickware plugin is active
        if ($this->pickwareActive) {
            $events = [Events::onFlush];
        }

        return $events;
    }

    /**
     * On Lifecycle on Flush Doctrine Event
     *
     * @param OnFlushEventArgs $eventArgs
     *
     * @return void
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $class = get_class($entity);

            if (!in_array($class, [OrderDetail::class, Order::class], true)) {
                continue;
            }

            $changeSet = $unitOfWork->getEntityChangeSet($entity);
            $event = new PreUpdateEventArgs($entity, $entityManager, $changeSet);

            if ($entity instanceof OrderDetail && $this->isPayolutionOrder($entity->getOrderDetail()->getOrder())) {
                $this->getOrderModelHandler()->processAttributeUpdate($event);
            }

            if ($entity instanceof Order && $this->isPayolutionOrder($entity)) {
                $this->getOrderModelHandler()->processOrderUpdate($event);
            }
        }
    }

    /**
     * Get the order model handler
     *
     * The handler mus be included over the container at the runtime because
     * we are in the lifecycle context of an doctrine entity
     *
     * If we try to inject the service directly we create an circular reference in the injected repositories
     * down the dependency chain
     *
     * @return object|OrderModelHandler
     */
    private function getOrderModelHandler()
    {
        if (!$this->orderModelHandler) {
            $this->orderModelHandler = $this->container->get('payolution_pickware.position.order_model_handler');
        }

        return $this->orderModelHandler;
    }

    /**
     * Check that the entity is an payolution order
     *
     * @param mixed $entity The entity from the uow
     *
     * @return bool
     */
    private function isPayolutionOrder($entity)
    {
        $order = null;
        if ($entity instanceof OrderDetail && ($detail = $entity->getOrderDetail())) {
            $order = $detail->getOrder();
        }

        if ($entity instanceof Order) {
            $order = $entity;
        }

        return $order && ($payment = $order->getPayment()) && ($payment->getAction() === 'PolPaymentPayolution');
    }
}