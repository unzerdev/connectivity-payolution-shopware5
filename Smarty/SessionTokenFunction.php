<?php

namespace PolPaymentPayolution\Smarty;

use Payolution\Session\SessionTokenStorage;
use Smarty_Internal_Template;

/**
 * Smarty function to return an session token
 *
 * @package PolPaymentPayolution\Smarty
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SessionTokenFunction
{
    /**
     * @var SessionTokenStorage
     */
   private $tokenStorage;

    /**
     * SessionTokenFunction constructor.
     *
     * @param SessionTokenStorage $tokenStorage
     */
    public function __construct(SessionTokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get the token
     *
     * @param array $params
     * @param Smarty_Internal_Template|null $template
     *
     * @return string
     */
    public function getToken($params = [], Smarty_Internal_Template $template = null)
    {
        return $this->tokenStorage->getToken();
    }
}