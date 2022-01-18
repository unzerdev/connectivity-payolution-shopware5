<?php

namespace Payolution\Enum;

/**
 * Class B2B
 *
 * @package Payolution\Enum
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
final class B2B
{
    /**
     * @var string B2B Type Mapping
     */
    const TYPE_MAPPING = [
        'company' => 'COMPANY',
        'soletrader' => 'SOLE',
        'authority' => 'PUBLIC',
        'society' => 'REGISTERED',
        'other' => 'OTHER'
    ];

    /**
     * B2B constructor.
     */
    private function __construct()
    {
    }
}
