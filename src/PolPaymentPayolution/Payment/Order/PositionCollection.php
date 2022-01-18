<?php

namespace PolPaymentPayolution\Payment\Order;

use Countable;

/**
 * Class PositionCollection
 *
 * @package PolPaymentPayolution\Payment\Order
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PositionCollection implements Countable
{
    /**
     * @var OrderPosition[]
     */
    private $positions;

    /**
     * PositionCollection constructor.
     *
     * @param OrderPosition[] $positions
     */
    public function __construct(array $positions)
    {
        $this->positions = $positions;
    }

    /**
     * Get Positions
     *
     * @return OrderPosition[]
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Add Position
     *
     * @param OrderPosition $position
     */
    public function addPosition(OrderPosition $position)
    {
        $this->positions [] = $position;
    }

    /**
     * Modify Position
     *
     * @param OrderPosition $position
     * @param int $index
     *
     * @return void
     */
    public function modifyPosition(OrderPosition $position, $index)
    {
        $this->positions[$index] = $position;
    }

    /**
     * Delete Position By Id
     *
     * @param string $identifier
     *
     * @return void
     */
    public function deletePositionById($identifier)
    {
        foreach ($this->positions as $key => $value) {
            if ($value->getIdentifier()->getIdentifier() === $identifier) {
                $this->deletePosition($key);
            }
        }
    }

    /**
     * Delete Position
     *
     * @param int $index
     *
     * @return void
     */
    public function deletePosition($index)
    {
        unset($this->positions[$index]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer The return value is cast to an integer.
     *
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->positions);
    }
}