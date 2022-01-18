<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use DateTime;
use PolPaymentPayolution\Exception\SaveHandlerException;
use PolPaymentPayolution\Payment\SaveHandler\Context\SaveContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class B2BSaveHandler
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class B2BSaveHandler extends AbstractSaveHandler
{
    protected $tableName = 'bestit_payolution_b2b';

    protected $tableAlias = 'b2b';

    /**
     * Saves the given payment data
     *
     * @param array $paymentData Array of payment data send by the form
     *
     * @return SaveContext Returns SaveContext containing success state and normalized payment data
     */
    public function save(array $paymentData)
    {
        $validationContext = $this->validate($paymentData);

        $result = false;
        $error = null;
        if ($validationContext->isSuccess()) {
            try {
                $this->savePaymentDataByType($paymentData['companyType'], $paymentData);
                $result = true;
            } catch (SaveHandlerException $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = $validationContext->getError();
        }

        return new SaveContext($result, $validationContext->getPaymentData(), $error);
    }

    /**
     * @inheritDoc
     */
    public function supports($shortCut)
    {
        return $shortCut === 'b2b';
    }

    /**
     * Configures the OptionsResolver to validate the payment data array.
     *
     * @param array $paymentData Array of payment data send by the form
     *
     * @return OptionsResolver Returns the OptionsResolver
     */
    protected function getOptionsResolver(array &$paymentData)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired('companyType');
        $resolver->setRequired('company');
        $resolver->setRequired('vatid');

        $resolver->addAllowedValues('companyType', 'company');
        $resolver->addAllowedValues('companyType', 'soletrader');
        $resolver->addAllowedValues('companyType', 'authority');
        $resolver->addAllowedValues('companyType', 'society');
        $resolver->addAllowedValues('companyType', 'others');

        $resolver->addAllowedValues('company', function ($optionValue) {
            return $optionValue !== '';
        });

        $resolver->addAllowedValues('vatid', function ($optionValue) {
            return $optionValue !== '';
        });

        if (isset($paymentData['companyType']) && $paymentData['companyType'] === 'soletrader') {
            $this->addBirthdayResolver($resolver);
        } else {
            unset($paymentData['birthday'], $paymentData['birthyear'], $paymentData['birthmonth']);
        }

        $resolver->setRequired('firstname');
        $resolver->setRequired('lastname');

        $resolver->addAllowedTypes('firstname', 'string');
        $resolver->addAllowedTypes('lastname', 'string');

        $resolver->addAllowedValues('firstname', function ($optionValue) {
            return $optionValue !== '';
        });

        $resolver->addAllowedValues('lastname', function ($optionValue) {
            return $optionValue !== '';
        });

        $this->addCheckboxResolver($resolver, 'privacyCheck');

        return $resolver;
    }

    /**
     * Save Payment Data By Type
     *
     * @param string $type
     * @param array $paymentData
     * @return bool
     *
     * @throws SaveHandlerException
     */
    private function savePaymentDataByType($type, $paymentData)
    {
        if (!$userId = $this->getUserId()) {
            throw new SaveHandlerException('Unknown user given');
        }

        $qb = $this->buildBaseQuery();

        $this->addSetElementQuery($qb, 'type', $type);
        $this->addSetElementQuery($qb, 'userId', $userId);
        $this->addSetElementQuery($qb, 'company', $paymentData['company']);
        $this->addSetElementQuery($qb, 'vat', $paymentData['vatid']);

        if ($type === 'soletrader') {
            $this->saveBirthdayData($paymentData);
            $this->addSetElementQuery($qb, 'firstName', $paymentData['firstname']);
            $this->addSetElementQuery($qb, 'lastName', $paymentData['lastname']);

            $date = (new DateTime())
                ->setDate(
                    $paymentData['birthyear'],
                    $paymentData['birthmonth'],
                    $paymentData['birthday']
                )->format('Y-m-d');

            $this->addSetElementQuery($qb, 'birthday', $date);
        }

        return $this->executeQuery($qb);
    }
}
