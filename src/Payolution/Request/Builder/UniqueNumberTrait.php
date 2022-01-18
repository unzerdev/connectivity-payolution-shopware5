<?php

namespace Payolution\Request\Builder;

/**
 * Class UniqueNumberTrait
 *
 * @package Payolution\Request\Builder
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
trait UniqueNumberTrait
{
    /**
     * Generate Unique ID
     *
     * @return string
     */
    private function generateUniqueId()
    {
        return  md5(uniqid(mt_rand(), true));
    }
}
