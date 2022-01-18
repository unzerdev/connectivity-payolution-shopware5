<?php

namespace PolPaymentPayolution\Payment\Order;

/**
 * Class OrderPositionIdentifier
 *
 * @package PolPaymentPayolution\Payment\Order
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class OrderPositionIdentifier
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int
     */
    private $additionalIdentifier;

    /**
     * OrderPositionIdentifier constructor.
     *
     * @param string $identifier
     * @param int|null $additionalIdentifier
     */
    public function __construct($identifier, $additionalIdentifier = null)
    {
        $this->identifier = $identifier;
        $this->additionalIdentifier = $additionalIdentifier;
    }

    /**
     * Get Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get AdditionalIdentifier
     *
     * @return int|null
     */
    public function getAdditionalIdentifier()
    {
        return $this->additionalIdentifier;
    }
}