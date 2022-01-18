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
    private $preCheck;

    /**
     * @var bool
     */
    private $taxFree;

    /**
     * RequestOptions constructor.
     *
     * @param array $basket
     * @param array $user
     * @param bool $taxFree
     * @param bool $preCheck
     */
    public function __construct(array $basket, array $user, $taxFree, $preCheck)
    {
        $this->basket = $basket;
        $this->user = $user;
        $this->taxFree = $taxFree;
        $this->preCheck = $preCheck;
    }

    /**
     * Get Basket
     *
     * @return array
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Get User
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Is PreCheck
     *
     * @return bool
     */
    public function isPreCheck()
    {
        return $this->preCheck;
    }

    /**
     * Is TaxFree
     *
     * @return bool
     */
    public function isTaxFree()
    {
        return $this->taxFree;
    }
}
