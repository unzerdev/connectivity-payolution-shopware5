<?php

namespace PolPaymentPayolution\Util\Session;

/**
 * Interface SessionManagerInterface
 *
 * @package PolPaymentPayolution\Util\Session
 */
interface SessionManagerInterface
{
    /**
     * Get session value
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null);

    /**
     * Set session value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);

    /**
     * Check if Key Exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Unset Value by Key
     *
     * @param string $key
     *
     * @return void
     */
    public function remove($key);
}
