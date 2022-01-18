<?php

namespace Payolution\Request\Builder;

/**
 * Interface RequestBuilderInterface
 *
 * @package Payolution\Request\Builder
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
interface RequestBuilderInterface
{
    /**
     * Supports Builder Request
     *
     * @param string $mode
     * @return bool
     */
    public function supports($mode);

    /**
     * Build Request Array
     *
     * @param RequestOptions $options
     * @param RequestContext $context
     * @return array
     */
    public function buildRequest(RequestOptions $options, RequestContext $context);
}
