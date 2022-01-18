<?php

namespace PolPaymentPayolution\Payment\Validator\Constraints;

use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for the min age constraint
 *
 * @package PolPaymentPayolution\Payment\Validator\Constraints
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class MinAgeConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $date = null;

        /** @var MinAgeConstraint $minAgeConstraint */
        $minAgeConstraint = $constraint;

        if (is_string($value)) {
            $date = date_create_from_format('Y-m-d', $value);
        } elseif ($value instanceof DateTime) {
            $date = $value;
        }

        if ($date && ($date->modify(sprintf('+%syears', $minAgeConstraint->minAge)) >= new DateTime())) {
            $this->context->buildViolation($minAgeConstraint->message)->addViolation();
        }
    }
}
