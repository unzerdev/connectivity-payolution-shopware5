<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use PDO;
use PolPaymentPayolution\Exception\InstallmentException;
use PolPaymentPayolution\Exception\SaveHandlerException;
use PolPaymentPayolution\Payment\SaveHandler\Context\SaveContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InstallmentSaveHandler
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class InstallmentSaveHandler extends AbstractSaveHandler
{
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
                $this->saveBirthdayData($paymentData);
                $this->saveInstallmentInfos($paymentData);
                $result = true;
            } catch (SaveHandlerException $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = $validationContext->getError();
        }

        return new SaveContext($result, $paymentData, $error);
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

        $userData = $this->getUserData();

        $country = $userData['additional']['country'];

        // Bank details are not displayed for switzerland
        if ($country['countryiso'] !== 'CH' && $country['iso3'] !== 'CHE') {
            $this->addBankDetailsResolver($resolver);
        }

        $this->addBirthdayResolver($resolver);
        $this->addCheckboxResolver($resolver, 'privacyCheck');
        $this->addInstallmentResolver($resolver);

        return $resolver;
    }

    /**
     * Configures OptionsResolver for installment specific fields (amount)
     *
     * @param OptionsResolver $resolver Instance of OptionsResolver
     *
     * @return void
     */
    private function addInstallmentResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired('duration');
        $resolver->setAllowedTypes('duration', 'string');
        $resolver->setNormalizer('duration', function (OptionsResolver $optionsResolver, $optionValue) {
            return (int)$optionValue;
        });
    }

    /**
     * @inheritDoc
     */
    public function supports($shortCut)
    {
        return $shortCut === 'installment';
    }

    /**
     * Save Debit Infos
     *
     * @param array $paymentData
     *
     * @throws SaveHandlerException
     *
     * @return void
     */
    private function saveInstallmentInfos(array $paymentData)
    {
        if (isset($paymentData['duration']) && $userId = $this->getUserId()) {
            $currentInfos = $this->getCurrentInstallmentInfos($userId);
            $duration = $paymentData['duration'];
            try {
                $amount = $this->getAmountForInstallment($userId, $duration);
            } catch (InstallmentException $e) {
                throw new SaveHandlerException($e->getMessage());
            }

            $holder = isset($paymentData['holder']) ? $paymentData['holder'] : null;
            $iban = isset($paymentData['iban']) ? $paymentData['iban'] : null;
            $bic = isset($paymentData['bic']) ? $paymentData['bic'] : null;

            $currentHash = $this->calculateInstallmentHash($currentInfos);
            $newHash = $this->calculateInstallmentHash([
                'userId' => $userId,
                'amount' => $amount,
                'duration' => $paymentData['duration'],
                'accountHolder' => $holder,
                'accountBic' => $bic,
                'accountIban' => $iban
            ]);

            if ($currentHash === $newHash) {
                return;
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->update('bestit_payolution_installment', 'ps')
                ->set('ps.userId', ':userId')
                ->set('ps.amount', ':amount')
                ->set('ps.duration', ':duration')
                ->set('ps.accountHolder', ':accountHolder')
                ->set('ps.accountBic', ':accountBic')
                ->set('ps.accountIban', ':accountIban')
                ->where($qb->expr()->eq('ps.userId', ':userId'));

            $qb->setParameters(
                [
                    'userId' => $userId,
                    'accountHolder' => $holder,
                    'accountBic' => $bic,
                    'accountIban' => $iban,
                    'amount' => $amount,
                    'duration' => $paymentData['duration']
                ]
            );

            if ($qb->execute() !== 1) {
                throw new SaveHandlerException(
                    sprintf(
                        'Error in updating installment data with query "%s" and params %s',
                        $qb->getSQL(),
                        json_encode($qb->getParameters())
                    )
                );
            }
        } else {
            throw new SaveHandlerException('Duration is not set');
        }
    }

    /**
     * Get Current Debit Info
     *
     * @param int $userId
     * @return array
     */
    private function getCurrentInstallmentInfos($userId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from('bestit_payolution_installment', 'ps')
            ->select('ps.userId, ps.amount, ps.duration, ps.accountHolder, ps.accountBic, ps.accountIban')
            ->where($qb->expr()->eq('ps.userId', ':userId'))
            ->setParameter('userId', $userId);

        $result = $qb->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (isset($result[0])) {
            return $result[0];
        }

        return [];
    }

    /**
     * Calculate Debit Info Hash
     *
     * @param array $debitInfos
     * @return string
     */
    private function calculateInstallmentHash(array $debitInfos)
    {
        return sha1(json_encode($debitInfos));
    }

    /**
     * Get Amount For Installment
     *
     * @param int $userId
     * @param string $duration
     *
     * @return int
     *
     * @throws InstallmentException
     */
    private function getAmountForInstallment($userId, $duration)
    {
        $amount = 0;

        $qb = $this->connection->createQueryBuilder();
        $qb->from('bestit_payolution_installment', 'ps')
            ->select('ps.request')
            ->where($qb->expr()->eq('ps.userId', ':userId'))
            ->setParameter('userId', $userId);

        if (!$result = $qb->execute()->fetch(PDO::FETCH_COLUMN)) {
            throw new InstallmentException(
                sprintf(
                    'Error in query installment data with query "%s" and params %s',
                    $qb->getSQL(),
                    json_encode($qb->getParameters())
                )
            );
        }

        if (is_string($result) && $result && $result !== '') {
            $request = json_decode($result, true);

            if (isset($request['PaymentDetails']) && is_array($request['PaymentDetails'])) {
                foreach ($request['PaymentDetails'] as $detail) {
                    if (isset($detail['Duration'], $detail['TotalAmount']) && (int)$detail['Duration'] === $duration) {
                        $amount = $detail['TotalAmount'];
                        break;
                    }
                }
            }
        }

        if ($amount === 0) {
            throw new InstallmentException(
                sprintf(
                    'Invalid Amount for installment with given duration %s, amount mus be larger than 0',
                    $duration
                )
            );
        }

        return $amount;
    }
}
