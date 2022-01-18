<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use DateTime;
use Exception;
use Payolution\BirthdayValidation\CheckBirthday;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Exception\SaveHandlerException;
use PolPaymentPayolution\Payment\SaveHandler\Context\ValidationContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractSaveHandler
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class AbstractSaveHandler implements SaveHandlerInterface
{
    use QueryBuilderTrait;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * AbstractSaveHandler constructor.
     *
     * @param ComponentManagerInterface $componentManager
     */
    public function __construct(ComponentManagerInterface $componentManager)
    {
        $this->componentManager = $componentManager;
        $this->connection = $this->componentManager->getDbalConnection();
    }

    /**
     * Validates the given payment data.
     *
     * @param array $paymentData Array of payment data send by the form
     *
     * @return ValidationContext Returns a validation context with success state and paymentData
     */
    final public function validate(array &$paymentData)
    {
        $resolver = $this->getOptionsResolver($paymentData);

        $success = true;
        $error = null;
        try {
            $paymentData = $resolver->resolve($paymentData);
        } catch (Exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        return new ValidationContext($success, $paymentData, $error);
    }

    /**
     * Configures the OptionsResolver to validate the payment data array.
     *
     * @param array $paymentData Array of payment data send by the form
     *
     * @return OptionsResolver Returns the OptionsResolver
     */
    abstract protected function getOptionsResolver(array &$paymentData);

    /**
     * Configures OptionsResolver for birthday fields.
     *
     * @param OptionsResolver $resolver Instance of OptionsResolver to configure
     *
     * @return void
     */
    protected function addBirthdayResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired('birthday');
        $resolver->setRequired('birthmonth');
        $resolver->setRequired('birthyear');

        $resolver->setAllowedTypes('birthday', 'string');
        $resolver->setAllowedTypes('birthmonth', 'string');
        $resolver->setAllowedTypes('birthyear', 'string');

        $resolver->setAllowedValues('birthday', function ($value) {
            $valueAsInteger = (int)$value;

            return $valueAsInteger > 0 && $valueAsInteger <= 31;
        });

        $resolver->setAllowedValues('birthmonth', function ($value) {
            $valueAsInteger = (int)$value;

            return $valueAsInteger > 0 && $valueAsInteger <= 12;
        });

        $resolver->setAllowedValues('birthyear', function ($value) {
            $valueAsInteger = (int)$value;

            return $valueAsInteger >= 1900 && $valueAsInteger <= (int)date('Y');
        });
    }

    /**
     * Configures OptionsResolver for given checkbox field.
     *
     * @param OptionsResolver $resolver Instance of OptionsResolver
     * @param string $fieldName Name of the field containing the value of the checkbox
     *
     * @return void
     */
    protected function addCheckboxResolver(OptionsResolver $resolver, $fieldName)
    {
        $resolver->setRequired($fieldName);
        $resolver->setAllowedTypes($fieldName, 'string');
        $resolver->setAllowedValues($fieldName, 'on');
        $resolver->setNormalizer($fieldName, function (OptionsResolver $optionsResolver, $optionValue) {
            return $optionValue === 'on';
        });
    }

    /**
     * Configures OptionsResolver for bank detail fields (holder, iban, bic)
     *
     * @param OptionsResolver $resolver Instance of OptionsResolver
     *
     * @return void
     */
    protected function addBankDetailsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired('holder');
        $resolver->setRequired('iban');
        $resolver->setRequired('bic');

        $resolver->setAllowedTypes('holder', 'string');
        $resolver->setAllowedTypes('iban', 'string');
        $resolver->setAllowedTypes('bic', 'string');

        $resolver->setAllowedValues('holder', function ($optionValue) {
            return $optionValue !== '';
        });

        $resolver->setAllowedValues('iban', function ($optionValue) {
            return $optionValue !== '';
        });

        $resolver->setAllowedValues('bic', function ($optionValue) {
            return $optionValue !== '';
        });
    }

    /**
     * Saves the birthday out of paymentData
     *
     * @param array $paymentData Array of payment data to be saved
     *
     * @throws SaveHandlerException
     *
     * @return void
     */
    protected function saveBirthdayData(array $paymentData)
    {
        if (isset($paymentData['birthday'], $paymentData['birthmonth'], $paymentData['birthyear'])
            && $userId = $this->getUserId()) {
            $date = (new DateTime())
                ->setDate(
                    $paymentData['birthyear'],
                    $paymentData['birthmonth'],
                    $paymentData['birthday']
                )->format('Y-m-d');

            // check if date is valid
            if (!CheckBirthday::validateBirthday($date)) {
                throw new SaveHandlerException('Birthday is invalid, not over 18');
            }

            // skip if date is already set
            if ($date === $this->getBirthDay()) {
                return;
            }

            $qb = $this->componentManager->getDbalConnection()->createQueryBuilder();
            $qb->update('s_user', 'su')
                ->set('su.birthday', ':birthday')
                ->where($qb->expr()->eq('su.id', ':id'))
                ->setParameter(
                    'birthday',
                    $date
                )
                ->setParameter('id', $userId);

            if ($qb->execute() !== 1) {
                throw new SaveHandlerException(
                    sprintf(
                        'Can\'t update Birthday with date %s for userId %s and query %s:%s',
                        $date,
                        $userId,
                        $qb->getSQL(),
                        json_encode($qb->getParameters())
                    )
                );
            }
        } else {
            throw new SaveHandlerException(
                sprintf(
                    'Can\'t update Birthday invalid data %s',
                    json_encode($paymentData)
                )
            );
        }
    }

    /**
     * Save Phone
     *
     * @param array $paymentData
     * @return bool
     */
    protected function savePhone(array $paymentData)
    {
        $result = false;

        if (isset($paymentData['phone']) && $userId = $this->getUserId()) {
            $phone = $paymentData['phone'];

            $resultQuery = $this->componentManager->getDatabase()->query(
                'UPDATE
                s_user_addresses sua
              INNER JOIN
                s_user su
              ON
                sua.id = su.default_billing_address_id
            SET
              phone = :phone
            WHERE
              user_id = :userId',
                [
                    ':phone' => $phone,
                    ':userId' => $userId,
                ]
            );

            if ($resultQuery) {
                return true;
            }
        }
        return $result;
    }

    /**
     * Get Birthday
     *
     * @return string|null
     */
    protected function getBirthDay()
    {
        $userData = $this->getUserData();

        $birthday = null;
        if ($userData && isset($userData['additional']['user']['birthday'])) {
            $birthday = $userData['additional']['user']['birthday'];
        }

        return $birthday;
    }

    /**
     * Get UserId
     *
     * @return null|string
     */
    protected function getUserId()
    {
        $userData = $this->getUserData();

        $id = null;
        if ($userData && isset($userData['additional']['user']['id'])) {
            $id = $userData['additional']['user']['id'];
        }

        return $id;
    }

    /**
     * Get User Data
     *
     * @return array
     */
    protected function getUserData()
    {
        static $userData = [];

        if (!$userData) {
            $userData = $this->componentManager->getAdminModule()->sGetUserData();
        }

        return $userData;
    }
}
