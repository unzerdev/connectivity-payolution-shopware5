<?php

namespace PolPaymentPayolution\Payment\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Constraint for min age checks
 *
 * @package PolPaymentPayolution\Payment\Validator\Constraints
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class MinAgeConstraint extends Constraint
{
    /**
     * @var int
     */
    public $minAge;

    /**
     * @var string
     */
    public $message = 'Invalid Date given';

    /**
     * MinAgeConstraint constructor.
     *
     * @param null|mixed $options
     */
    public function __construct($options = null)
    {
        if ($options !== null && !is_array($options)) {
            $options = [
                'minAge' => $options
            ];
        }

        parent::__construct($options);

        if (!$this->minAge) {
            throw new MissingOptionsException('Missing min age option', $options);
        }
    }
}
