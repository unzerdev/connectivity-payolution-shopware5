<?php

namespace PolPaymentPayolution\Payment\Validator\Validators;

use Exception;
use Payolution\Request\B2B\InfoResolver;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Payment\Validator\Constraints\MinAgeConstraint;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * B2B payment validator
 *
 * @package PolPaymentPayolution\Payment\Validator\Validators
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class B2BValidator implements PaymentValidatorInterface
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
     * B2BValidator constructor.
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
        $result = true;
        $infos = $this->loadB2BInfos();

        try {
            $normalizedInfos = (new InfoResolver())->resolve($infos);
        } catch (Exception $e) {
            $result = false;
        }

        if ($result) {
            $type = $normalizedInfos['type'];
            $violations = $this->validator->validate($infos, $this->buildConstraints($type));

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
        return $payment === 'payolution_invoice_b2b';
    }

    /**
     * Build Constraint
     *
     * @param $type
     * @return Collection
     */
    private function buildConstraints($type)
    {
        $constraint = new Collection(
            $constraints = [
                'type' => new NotBlank(),
                'vat' => new NotBlank(),
                'userId' => new NotBlank(),
                'company' => new NotBlank(),
                'firstName' => new Blank(),
                'lastName' => new Blank(),
                'birthday' => new Optional(),
            ]
        );

        if ($type === 'soletrader') {
            $constraints['firstName'] = new NotBlank();
            $constraints['lastName'] = new NotBlank();
            $constraints['birthday'] = $this->minAgeConstraint;
        }

        return $constraint;
    }

    /**
     * Load B2B Info
     **
     * @return array
     */
    private function loadB2BInfos()
    {
        $infos = [];
        $user = $this->componentManager->getAdminModule()->sGetUserData();
        if ($user & is_array($user)) {
            $userId = $user['additional']['user']['userID'];

            $result = $this
                ->componentManager
                ->getDatabase()
                ->fetchRow('SELECT * FROM bestit_payolution_b2b WHERE userId = ?', $userId);

            if (is_array($result)) {
                $infos = $result;
            }
        }

        return $infos;
    }
}
