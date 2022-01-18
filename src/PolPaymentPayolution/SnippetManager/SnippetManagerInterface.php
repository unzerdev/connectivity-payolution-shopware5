<?php

namespace PolPaymentPayolution\SnippetManager;

/**
 * Interface SnippetManagerInterface
 *
 * @package PolPaymentPayolution\SnippetManager
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
interface SnippetManagerInterface
{
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
    public function getByName($name, $nameSpace , $default = null, $save = true);
}