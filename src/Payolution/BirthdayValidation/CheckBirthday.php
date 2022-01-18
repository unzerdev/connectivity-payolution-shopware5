<?php

namespace Payolution\BirthdayValidation;

use Exception;

/**
 * Class CheckBirthday
 * @package Payolution\BirthdayValidation
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class CheckBirthday
{
    /**
     * Check if user is over 18 years old.
     *
     * @param string $birthday
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function validateBirthday($birthday)
    {
        if (!$date = date_create_from_format('Y-m-d', $birthday)) {
            return false;
        }

        return $date->modify('+18years') <= new \DateTime();
    }
}
