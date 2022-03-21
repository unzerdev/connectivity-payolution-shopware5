<?php

namespace Payolution\Request\Builder;

/**
 * Class RequestOptions
 *
 * @package Payolution\Request\Builder
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RequestOptions
{
    /**
     * @var array
     */
    private $basket;

    /**
     * @var array
     */
    private $user;

    /**
     * @var bool
     */
    private $taxFree;

    /**
     * @var bool
     */
    private $isPreCheck;

    public function __construct(array $basket, array $user, bool $taxFree, bool $isPreCheck = false)
    {
        $this->basket = $basket;
        $this->user = $user;
        $this->taxFree = $taxFree;
        $this->isPreCheck = $isPreCheck;
    }

    public function getBasket(): array
    {
        return $this->basket;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function isTaxFree(): bool
    {
        return $this->taxFree;
    }

    public function isPreCheck(): bool
    {
        return $this->isPreCheck;
    }
}
