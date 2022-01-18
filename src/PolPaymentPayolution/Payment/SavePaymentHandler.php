<?php

namespace PolPaymentPayolution\Payment;

use Exception;
use InvalidArgumentException;
use PolPaymentPayolution\Payment\SaveHandler\SaveHandlerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Models\Payment\Payment;

/**
 * Class SavePaymentHandler
 *
 * @package PolPaymentPayolution\Payment
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class SavePaymentHandler
{
    /**
     * @var SaveHandlerInterface[]
     */
    private $saveHandlers = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SavePaymentHandler constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Add Safe Handler
     *
     * @param SaveHandlerInterface $saveHandler
     */
    public function addSaveHandler(SaveHandlerInterface $saveHandler)
    {
        $this->saveHandlers[] = $saveHandler;
    }

    /**
     * Processes the save payment operation.
     *
     * @param array $requestParams Represents the form values of saveShippingPayment
     *
     * @return void
     *
     * @throws Exception
     */
    public function process(array $requestParams)
    {
        $this->logger->debug(
            'Start Processing requestParams from paymentShipping',
            [
                'requestParams' => json_encode($requestParams),
                'name' => 'SavePaymentHandler'
            ]
        );

        if (isset($requestParams['isXHR']) && $requestParams['isXHR'] === '1') {
            $this->logger->debug('Skip processing params xhr detected', ['name' => 'SavePaymentHandler']);
            return;
        }

        $payment = $this->getPayment($requestParams);

        if (!$this->isPayolutionPayment($payment)) {
            $this->logger->debug('Skip processing params no payol payment', ['name' => 'SavePaymentHandler']);
            return;
        }

        $paymentShortcut = $this->getPaymentShortcut($requestParams);

        $paymentData = $this->getPaymentData($paymentShortcut, $requestParams);

        if (count($paymentData) === 0) {
            $this->logger->error('Abort save payment data, invalid data', ['name' => 'SavePaymentHandler']);
            return;
        }

        $saveContext = null;
        foreach ($this->saveHandlers as $saveHandler) {
            if ($saveHandler->supports($paymentShortcut)) {
                $saveContext = $saveHandler->save($paymentData);
                break;
            }
        }

        if ($saveContext && !$saveContext->isSuccess()) {
            $this->logger->error(
                sprintf(
                    'Error in save data with error %s',
                    $saveContext->getError()
                ),
                ['name' => 'SavePaymentHandler']
            );
        }
    }

    /**
     * Returns the paymentId
     *
     * @param array $requestParams Array of request parameters
     *
     * @return int Returns id of the payment
     */
    private function getPaymentId(array $requestParams)
    {
        if ($requestParams['controller'] === 'account') {
            return $requestParams['register']['payment'];
        }

        return $requestParams['payment'];
    }

    /**
     * Returns the payment model
     *
     * @param array $requestParams Array of request parameters
     *
     * @return Payment Returns a payment instance
     */
    private function getPayment(array $requestParams)
    {
        $paymentRepository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');

        $paymentId = $this->getPaymentId($requestParams);

        $payment = $paymentRepository->find($paymentId);

        if ($payment === null) {
            throw new InvalidArgumentException('Given paymentId #' . $paymentId . ' is invalid.');
        }

        return $payment;
    }

    /**
     * Checks if the given payment is a payolution payment.
     *
     * @param Payment $payment The payment model.
     *
     * @return bool Returns true if the given payment is a payolution payment or false
     */
    private function isPayolutionPayment(Payment $payment)
    {
        return $payment->getAction() === 'PolPaymentPayolution';
    }

    /**
     * Returns needed payment data from request params.
     *
     * @param string $paymentShortcut Shortcut of the payolution payment
     * @param array $requestParams All request parameters send by the change payment form
     *
     * @return array Returns subset of the $requestParams containing specific data for the selected payment
     */
    private function getPaymentData($paymentShortcut, array $requestParams)
    {
        // Check if submitted array contains the payolution form array
        // return empty array to indicate an error case
        if (!isset($requestParams['payolution'][$paymentShortcut])) {
            $this->logger->error('invalid submitted form data');

            return [];
        }

        return $requestParams['payolution'][$paymentShortcut];
    }

    /**
     * Returns the shortcut of the payolution payment.
     *
     * @param array $requestParams All request parameters send by the change payment form
     *
     * @return string Returns the shortcut of the selected payment
     */
    private function getPaymentShortcut(array $requestParams)
    {
        $payment = $this->getPayment($requestParams);

        $paymentNameFragments = explode('_', $payment->getName());

        $paymentNameFragments = array_reverse($paymentNameFragments);

        return $paymentNameFragments[0];
    }
}
