<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use PolPaymentPayolution\Exception\SaveHandlerException;
use PolPaymentPayolution\Payment\SaveHandler\Context\SaveContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class B2CSaveHandler
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class B2CSaveHandler extends AbstractSaveHandler
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
                $result = true;
            } catch (SaveHandlerException $e) {
                $error = $e->getMessage();
            }

            if (isset($paymentData['phone'])) {
                $result &= $this->savePhone($paymentData);
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

        if (isset($paymentData['phone'])) {
            $resolver->setRequired('phone');
            $resolver->setAllowedTypes('phone', 'string');
        }

        $this->addBirthdayResolver($resolver);
        $this->addCheckboxResolver($resolver, 'privacyCheck');

        return $resolver;
    }

    /**
     * @inheritDoc
     */
    public function supports($shortCut)
    {
        return $shortCut === 'b2c';
    }
}
