<?php

namespace PolPaymentPayolution\Payment\Validator\Validators;

use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Payment\Validator\Constraints\MinAgeConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * B2C payment validator
 *
 * @package PolPaymentPayolution\Payment\Validator\Validators
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class B2CValidator implements PaymentValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var MinAgeConstraint
     */
    private $minAgeConstraint;

    /**
     * B2CValidator constructor.
     *
     * @param ValidatorInterface $validator
     * @param ComponentManagerInterface $componentManager
     * @param MinAgeConstraint $minAgeConstraint
     */
    public function __construct(
        ValidatorInterface $validator,
        ComponentManagerInterface $componentManager,
        MinAgeConstraint $minAgeConstraint
    ) {
        $this->validator = $validator;
        $this->componentManager = $componentManager;
        $this->minAgeConstraint = $minAgeConstraint;
    }

    /**
     * Validate Payment with saved variables
     *
     * @return bool
     */
    public function validate()
    {
        $user = $this->componentManager->getAdminModule()->sGetUserData();

        $result = false;
        if ($user & is_array($user) && isset($user['additional']['user']['birthday'])) {
            $birthDay = $user['additional']['user']['birthday'];
            $violations = $this->validator->validate($birthDay, [$this->minAgeConstraint]);

            $result = $violations->count() === 0;
        }

        return $result;
    }

    /**
     * Checks if Validator supports payment
     *
     * @param string $payment
     *
     * @return bool
     */
    public function supports($payment)
    {
        return $payment === 'payolution_invoice_b2c';
    }
}
