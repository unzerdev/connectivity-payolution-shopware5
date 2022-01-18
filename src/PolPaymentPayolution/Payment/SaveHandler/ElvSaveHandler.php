<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use PDO;
use PolPaymentPayolution\Exception\SaveHandlerException;
use PolPaymentPayolution\Payment\SaveHandler\Context\SaveContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ElvSaveHandler
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ElvSaveHandler extends AbstractSaveHandler
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
                $result = $this->saveDebitInfos($paymentData);
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

        $this->addBirthdayResolver($resolver);
        $this->addBankDetailsResolver($resolver);
        $this->addCheckboxResolver($resolver, 'privacyCheck');
        $this->addCheckboxResolver($resolver, 'sepaMandate');

        return $resolver;
    }

    /**
     * @inheritDoc
     */
    public function supports($shortCut)
    {
        return $shortCut === 'elv';
    }

    /**
     * Save Debit Infos
     *
     * @param array $paymentData
     * @return bool
     */
    private function saveDebitInfos(array $paymentData)
    {
        $result = false;

        if (isset($paymentData['holder'], $paymentData['iban'], $paymentData['bic'])
            && $userId = $this->getUserId()) {
            $currentInfos = $this->getCurrentDebitInfo($userId);

            if ($this->calculateDebitHash($currentInfos) === $this->calculateDebitHash([
                    'userId' => $userId,
                    'accountHolder' => $paymentData['holder'],
                    'accountBic' => $paymentData['bic'],
                    'accountIban' => $paymentData['iban']
                ])) {
                return true;
            }

            $qb = $this->connection->createQueryBuilder();
            if (count($currentInfos) === 0) {
                $qb->insert('bestit_payolution_elv')
                    ->values(
                        [
                            'userId' => ':userId',
                            'accountHolder' => ':accountHolder',
                            'accountBic' => ':accountBic',
                            'accountIban' => ':accountIban'
                        ]
                    );
            } else {
                $qb->update('bestit_payolution_elv', 'elv')
                    ->set('elv.userId', ':userId')
                    ->set('elv.accountHolder', ':accountHolder')
                    ->set('elv.accountBic', ':accountBic')
                    ->set('elv.accountIban', ':accountIban')
                    ->where($qb->expr()->eq('elv.userId', ':userId'));
            }

            $qb->setParameters(
                [
                    'userId' => $userId,
                    'accountHolder' => $paymentData['holder'],
                    'accountBic' => $paymentData['bic'],
                    'accountIban' => $paymentData['iban']
                ]
            );

            $result = $qb->execute() === 1;
        }
        return $result;
    }

    /**
     * Get Current Debit Info
     *
     * @param int $userId
     * @return array
     */
    private function getCurrentDebitInfo($userId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from('bestit_payolution_elv', 'elv')
            ->select('elv.userId, elv.accountHolder, elv.accountBic, elv.accountIban')
            ->where($qb->expr()->eq('elv.userId', ':userId'))
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
    private function calculateDebitHash(array $debitInfos)
    {
        return sha1(json_encode($debitInfos));
    }
}
