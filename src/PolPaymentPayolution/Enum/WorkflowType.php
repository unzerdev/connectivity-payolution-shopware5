<?php

namespace PolPaymentPayolution\Enum;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;


/**
 * Class WorkflowType
 *
 * @package PolPaymentPayolution\Enum
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
final class WorkflowType extends Type
{
    /**
     * Workflow type
     *
     * @var string
     */
    const TYPE_NAME = 'workflowtype';

    /**
     * Capture type
     *
     * @var string
     */
    const CAPTURE = 'capture';

    /**
     * Refund type
     *
     * @var string
     */
    const REFUND = 'refund';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return sprintf("ENUM('%s', '%s')", self::CAPTURE, self::REFUND);
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    public function getName()
    {
        return self::TYPE_NAME;
    }
}
