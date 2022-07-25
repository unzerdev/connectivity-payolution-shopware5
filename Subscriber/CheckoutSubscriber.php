<?php
namespace PolPaymentPayolution\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Enlight_Controller_ActionEventArgs;
use Exception;
use Payolution\Request\Installment\Cl\CreatePostParams;
use Payolution\Request\RequestEnums;
use Payolution\Request\RequestWrapper;
use PolPaymentPayolution\Installment\Cl\CreateRequestArray;
use PolPaymentPayolution\Payment\Validator\PaymentValidatorManager;
use PolPaymentPayolution\RiskManagement\CheckPayment;
use PolPaymentPayolution\Util\Session\SessionManager;
use Psr\Log\LoggerInterface;
use Payolution\Request\Installment\Cl\InstallmentCl;
use Shopware_Components_Snippet_Manager;
use Zend_Db_Adapter_Exception;

/**
 * Class CheckoutSubscriber
 *
 * Listens to an event when customer goes through checkout
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Carsten Henkelmann <c.henkelmann@bestit-online.de>
 */
class CheckoutSubscriber implements SubscriberInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string
     */
    private $updateErrorInstallment;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CheckPayment
     */
    private $checkPayment;

    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $dbAdapter;

    /**
     * @var CreateRequestArray
     */
    private $createRequestArray;

    /**
     * @var CreatePostParams
     */
    private $createPostParams;

    /**
     * @var RequestWrapper
     */
    private $requestWrapper;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @var PaymentValidatorManager
     */
    private $paymentValidatorManager;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * Checkout constructor.
     *
     * @param LoggerInterface $logger
     * @param CheckPayment $checkPayment
     * @param Enlight_Components_Db_Adapter_Pdo_Mysql $dbAdapter
     * @param CreateRequestArray $createRequestArray
     * @param CreatePostParams $createPostParams
     * @param RequestWrapper $requestWrapper
     * @param Shopware_Components_Snippet_Manager $snippetManager
     * @param SessionManager $sessionManager
     * @param PaymentValidatorManager $paymentValidatorManager
     */
    public function __construct(
        LoggerInterface $logger,
        CheckPayment $checkPayment,
        Enlight_Components_Db_Adapter_Pdo_Mysql $dbAdapter,
        CreateRequestArray $createRequestArray,
        CreatePostParams $createPostParams,
        RequestWrapper $requestWrapper,
        Shopware_Components_Snippet_Manager $snippetManager,
        SessionManager $sessionManager,
        PaymentValidatorManager $paymentValidatorManager
    ) {
        $this->logger = $logger;
        $this->checkPayment = $checkPayment;
        $this->dbAdapter = $dbAdapter;
        $this->createRequestArray = $createRequestArray;
        $this->createPostParams = $createPostParams;
        $this->requestWrapper = $requestWrapper;
        $this->snippetManager = $snippetManager;
        $this->sessionManager = $sessionManager;
        $this->paymentValidatorManager = $paymentValidatorManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @inheritdoc
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onDispatchSecureFrontendCheckout'
        ];
    }

    /**
     * On secure dispatch checkout
     *
     * @param Enlight_Controller_ActionEventArgs $args
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Exception
     */
    public function onDispatchSecureFrontendCheckout(Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Request_RequestHttp $checkoutRequest */
        $checkoutRequest = $args->getRequest();

        $actionName = $checkoutRequest->getActionName();

        $this->logger->info(
            sprintf('Start Checkout Process for action %s', $actionName),
            [
                'name' => 'PayolutionCheckout'
            ]
        );

        if (in_array($actionName, array('shippingPayment', 'confirm'))) {
            $installment = false;

            $subject = $args->getSubject();

            $view = $subject->View();

            $this->logger->debug(
                'Set fraud prevention script active',
                [
                    'name' => 'PayolutionCheckout'
                ]
            );

            //Flag that fraud prevention should be used - see frontend/index/index.tpl & frontend/index/header.tpl
            $view->assign('payolutionIncludeFraudPrevention', true);

            $payments = $view->sPayments;
            $user = $view->sUserData;

            $basket = $view->sBasket;

            $this->parameters = array(
                0 => $user['additional']['user']['paymentID'],
                1 => $basket,
                2 => $user
            );

            $this->updateErrorInstallment = '
                  UPDATE
                    bestit_payolution_installment
                  SET
                    errorMessage = :errorMessage
                  WHERE
                    userId = :userId';

            unset($checksWithoutRequest);

            $checksWithoutRequest[0] = $this->checkPayment->checkDifferentAddresses($this->parameters);
            $checksWithoutRequest[2] = $this->checkPayment->checkRestrictions($this->parameters);

            $this->logger->debug(
                'Execute first checks without requests',
                [
                    'name' => 'PayolutionCheckout',
                    'result' =>  $checksWithoutRequest
                ]
            );

            $this->setErrorMessages($checksWithoutRequest);

            $doClRequest = false;
            $confirmRedirect = false;
            $installmentPcWithoutIban = false;
            foreach ($payments as $payment) {
                $paymentName = $payment['name'];
                $skip = (int) $user['additional']['user']['paymentID'] !== (int) $payment['id'];
                $this->logger->debug(
                    sprintf('Process payment %s', $paymentName),
                    [
                        'name' => 'PayolutionCheckout',
                        'skip' =>  $skip ? 'true' : 'false'
                    ]
                );

                if (!$skip) {
                    if ($payment['name'] === 'payolution_installment') {
                        $error = $this->dbAdapter->fetchRow(
                            'SELECT
                          errorMessage,
                          clId,
                          financeAmount,
                          currency,
                          pcId,
                          accountIban
                        FROM
                          bestit_payolution_installment
                        WHERE
                          userId = :userId',
                            array(
                                ':userId' => $user['additional']['user']['id'],
                            )
                        );

                        if (isset($basket['sAmountWithTax']) && !empty($basket['sAmountWithTax'])) {
                            $basket['sAmount'] = $basket['sAmountWithTax'];
                        }

                        $this->logger->debug(
                            'Query Installment Data',
                            [
                                'name' => 'PayolutionCheckout',
                                'payload' =>  json_encode($error)
                            ]
                        );

                        $errorMessage = $error['errorMessage'];
                        if (!empty($errorMessage)
                            || empty($error['clId'])
                            || (string) $basket['sAmount'] !== $error['financeAmount']
                            || Shopware()->Shop()->getCurrency()->getCurrency() !== $error['currency']
                        ) {
                            $doClRequest = true;
                            $confirmRedirect = true;
                        }
                        if (empty($error['pcId'])) {
                            $confirmRedirect = true;
                        }
                        if (!empty($error['pcId'])
                            && (empty($error['accountIban'])
                                && ($user['additional']['country']['countryiso'] !== 'CH'
                                    && $user['additional']['country']['iso3'] !== 'CHE'))
                            && $doClRequest === false
                        ) {
                            $confirmRedirect = true;
                            $installmentPcWithoutIban = true;
                        }
                        $installment = true;

                        $this->logger->debug(
                            'Installment checks',
                            [
                                'name' => 'PayolutionCheckout',
                                'doRequest' => $doClRequest ? 'true' : 'false',
                                'redirect' => $confirmRedirect ? 'true' : 'false',
                                'withoutIban' => $installmentPcWithoutIban ? 'true' : 'false'
                            ]
                        );
                        break;
                    } elseif (($payment['name'] === 'payolution_invoice_b2c'
                            || $payment['name'] === 'payolution_invoice_b2b')
                        && $user['additional']['country']['countryiso'] === 'NL') {

                        if (empty($user['billingaddress']['phone'])) {
                            $confirmRedirect = true;
                        }
                    }
                }
            }

            if ($actionName === 'shippingPayment') {
                if (empty($user['additional']['user']['id'])) {
                    $subject->redirect(array('controller' => 'checkout', 'action' => 'confirm'));
                } else {
                    $elvArray = $this->dbAdapter->fetchRow(
                        'SELECT
                          *
                        FROM
                          bestit_payolution_elv
                        WHERE
                          userId = :userId',
                        array(
                            ':userId' => $user['additional']['user']['id'],
                        )
                    );

                    $this->logger->debug(
                        'Fetched elv data',
                        [
                            'name' => 'PayolutionCheckout',
                            'elv' => json_encode($elvArray)
                        ]
                    );

                    $view->assign('payolutionElv', $elvArray);

                    if ($this->setErrorMessages($checksWithoutRequest, false) === true) {
                        $this->logger->debug(
                            'Deactivate installment',
                            [
                                'name' => 'PayolutionCheckout'
                            ]
                        );
                        $view->assign('payolutionDisplayNoneInstallment', 1);
                    } elseif ($doClRequest === true) {
                        if (isset($basket['sAmountWithTax']) && !empty($basket['sAmountWithTax'])) {
                            $basket['sAmount'] = $basket['sAmountWithTax'];
                        }

                        $requestParams = $this->createRequestArray->createArray(
                            $basket['sAmount'],
                            round($basket['sAmount'] - $basket['sAmountTax'], 2),
                            $user['additional']['country']['countryiso']
                        );

                        $data = new InstallmentCl();

                        foreach ($requestParams as $method => $value) {
                            $data->$method($value);
                        }

                        $this->logger->debug(
                            'Execute Installment Request',
                            [
                                'name' => 'PayolutionCheckout',
                                'data' => $data,
                            ]
                        );

                        $post_data = $this->createPostParams->createParams($data);
                        $return = $this->requestWrapper->doRequest($post_data, RequestEnums::CI_TYPE);

                        $this->logger->debug(
                            sprintf('Installment Response status %s', $return['Status']),
                            [
                                'name' => 'PayolutionCheckout',
                                'return' => $return,
                            ]
                        );

                        $return['PaymentDetails'] = array_reverse($return['PaymentDetails']);

                        if ($return['Status'] === 'OK') {
                            $this->dbAdapter->query(
                                '
                            REPLACE INTO
                              bestit_payolution_installment
                                (
                                    `userId`,
                                    `clId`,
                                    `request`,
                                    `financeAmount`,
                                    `currency`
                                )
                            VALUES
                                (
                                    :userId,
                                    :clId,
                                    :request,
                                    :financeAmount,
                                    :currency
                                )',
                                array(
                                    ':userId' => $user['additional']['user']['id'],
                                    ':clId' => $return['Identification']['UniqueID'],
                                    ':request' => json_encode($return),
                                    ':financeAmount' => $basket['sAmount'],
                                    ':currency' => Shopware()->Shop()->getCurrency()->getCurrency()
                                )
                            );

                            $this->logger->debug(
                                'Assign Installment plan to template',
                                [
                                    'name' => 'PayolutionCheckout'
                                ]
                            );
                            $view->assign('payolutionInstallmentArray', json_decode(json_encode($return)));
                        }
                    }

                    $company = $this->dbAdapter->fetchOne(
                        '
                    SELECT
                      `value`
                    FROM
                      bestit_payolution_config
                    WHERE
                      `name` = :name
                    AND
                      `shopId` = :shopId
                    AND
                      `currencyId` = :currencyId',
                        array(
                            ':name' => 'company',
                            ':shopId' => Shopware()->Shop()->getId(),
                            ':currencyId' => Shopware()->Shop()->getCurrency()->getId()
                        )
                    );

                    $this->logger->debug(
                        'Assign Company plan to template',
                        [
                            'name' => 'PayolutionCheckout',
                            'company' =>  json_encode($company)
                        ]
                    );
                    $view->assign('payolutionCompany', base64_encode($company));

                    if ($paymentName === 'payolution_installment' && $doClRequest === false) {
                        $request = $this->dbAdapter->fetchOne(
                            'SELECT
                          `request`
                        FROM
                          bestit_payolution_installment
                        WHERE
                          userId = :userId',
                            array(
                                ':userId' => $user['additional']['user']['id']
                            )
                        );

                        $this->logger->debug(
                            'Assign Installment plan to template from db',
                            [
                                'name' => 'PayolutionCheckout'
                            ]
                        );
                        $view->assign('payolutionInstallmentArray', json_decode($request));
                    }

                    if ($installmentPcWithoutIban === true) {
                        $view->assign('installmentPcWithoutIban', $installmentPcWithoutIban);
                    }

                    if ($this->sessionManager->has('b2bErrorMessage')) {
                        $errorMessage = $this->sessionManager->get('b2bErrorMessage');
                        $this->sessionManager->remove('b2bErrorMessage');
                    }

                    if (!empty($errorMessage)) {
                        $this->logger->debug(
                            'Errors!',
                            [
                                'message' => $errorMessage
                            ]
                        );

                        $messages = array();
                        $errorTypes = array();
                        $errors = explode(';', $errorMessage);

                        foreach ($errors as $error) {
                            if ($error === 'rejected') {
                                if ($installment === true) {
                                    $messages[] = $this->snippetManager
                                        ->getNamespace('frontend/pol_payment_payolution/installment')->get(
                                            'payolutionErrorMessageInstallment',
                                            'Die Ratenkauf&uuml;berpr&uuml;fung war leider nicht erfolgreich. Dies kann unterschiedliche Gr&uuml;nde haben,wie etwa fehlerhafte Eingabedaten, eine unbekannte Adresse, oder ein vor&uuml;bergehendes technisches Problem.Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                            true
                                        );
                                } else {
                                    $messages[] = $this->snippetManager
                                        ->getNamespace('frontend/pol_payment_payolution/payment')->get(
                                            'payolutionErrorMessageInvoice',
                                            'Diese Zahlung konnte nicht durchgef&uuml;hrt werden. Dies kann unterschiedliche Gr&uuml;nde haben,wie etwa fehlerhafte Eingabedaten, eine unbekannte Adresse, oder ein vor&uuml;bergehendes technisches Problem.Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                            true
                                        );
                                }
                                $errorTypes[] = $error;
                            } else {
                                switch ($error) {
                                    case 'value':
                                        $messages[] = $this->snippetManager
                                            ->getNamespace('frontend/pol_payment_payolution/checkout')->get(
                                                'payolutionErrorBasketValue',
                                                'Sie sind au&szlig;erhalb des erlaubten Warenkorbbereiches. Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                                true
                                            );
                                        $errorTypes[] = $error;
                                        break;
                                    case 'address':
                                        $messages[] = $this->snippetManager
                                            ->getNamespace('frontend/pol_payment_payolution/checkout')->get(
                                                'payolutionErrorDifferentAddresses',
                                                'Es sind keine abweichende Lieferadressen erlaubt. Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                                true
                                            );
                                        $errorTypes[] = $error;
                                        break;
                                    case 'currency':
                                        $messages[] = $this->snippetManager
                                            ->getNamespace('frontend/pol_payment_payolution/checkout')->get(
                                                'payolutionErrorRestrictedCurrency',
                                                'Das Bezahlen in dieser W&auml;hrung ist mit der ausgew&auml;hlten Zahlungsart nicht m&ouml;glich. Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                                true
                                            );
                                        $errorTypes[] = $error;
                                        break;
                                    case 'country':
                                        $messages[] = $this->snippetManager
                                            ->getNamespace('frontend/pol_payment_payolution/checkout')->get(
                                                'payolutionErrorRestrictedCountry',
                                                'Das Bezahlen in diesem Land ist mit der ausgew&auml;hlten Zahlungsart nicht m&ouml;glich. Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                                true
                                            );
                                        $errorTypes[] = $error;
                                        break;
                                    case 'birthday':
                                        $messages[] = $this->snippetManager
                                            ->getNamespace('frontend/pol_payment_payolution/checkout')->get(
                                                'payolutionErrorBirthday',
                                                'F&uuml;r Kauf auf Rechnung muss ein Geburtsdatum hinterlegt werden und Sie m&uuml;ssen &uuml;ber 18 Jahre alt sein. Bitte &uuml;berpr&uuml;fen Sie die angegebenen Daten, oder w&auml;hlen Sie ein anderes Zahlungsmittel.',
                                                true
                                            );
                                        $errorTypes[] = $error;
                                        break;
                                }
                            }
                        }

                        $this->logger->debug(
                            'Assign messages to template',
                            [
                                'name' => 'PayolutionCheckout',
                                'messages' => json_encode($messages),
                                'errors' => json_encode($errorTypes)
                            ]
                        );
                        $view->assign('sErrorMessages', $messages);
                        $view->assign('countryError', in_array('country', $errorTypes, true));
                        $view->assign('currencyError', in_array('currency', $errorTypes, true));

                        if ($paymentName === 'payolution_installment') {
                            $errorParam[':errorMessage'] = null;
                            $errorParam[':userId'] = $user['additional']['user']['id'];

                            $this->dbAdapter->query($this->updateErrorInstallment, $errorParam);
                        }
                    }
                }
            } elseif ($actionName === 'confirm') {
                if ($this->setErrorMessages($checksWithoutRequest) === true) {
                    $subject->redirect(
                        array(
                            'controller' => 'checkout',
                            'action' => 'shippingPayment',
                            'sTarget' => 'checkout'
                        )
                    );
                } elseif ($doClRequest === true && !empty($user['additional']['user']['id'])) {
                    if ($this->sessionManager->get('lastAction') === 'register/saveRegister') {
                        $subject->redirect(
                            array(
                                'controller' => 'checkout',
                                'action' => 'cart',
                            )
                        );
                    } else {
                        $error = $this->dbAdapter->fetchOne(
                            'SELECT
                          errorMessage
                        FROM
                          bestit_payolution_installment
                        WHERE
                          userId = :userId',
                            array(
                                ':userId' => $user['additional']['user']['id']
                            )
                        );
                        if (!empty($error['errorMessage']) || $confirmRedirect === true) {
                            $subject->redirect(
                                array(
                                    'controller' => 'checkout',
                                    'action' => 'shippingPayment',
                                    'sTarget' => 'checkout'
                                )
                            );
                        }
                    }
                } elseif ($confirmRedirect === true) {
                    if ($this->sessionManager->get('lastAction') === 'register/saveRegister') {
                        $subject->redirect(
                            array(
                                'controller' => 'checkout',
                                'action' => 'cart',
                            )
                        );
                    } else {
                        $subject->redirect(
                            array(
                                'controller' => 'checkout',
                                'action' => 'shippingPayment',
                                'sTarget' => 'checkout'
                            )
                        );
                    }
                } else {
                    if ($installment === false) {
                        if (!$this->paymentValidatorManager->validate($paymentName)) {
                            $confirmRedirect = true;
                        }

                        if ($confirmRedirect === true) {
                            if ($this->sessionManager->get('lastAction') === 'register/saveRegister') {
                                $subject->redirect(
                                    array(
                                        'controller' => 'checkout',
                                        'action' => 'cart',
                                    )
                                );
                            }
                        } else {
                            $checksWithRequest[0] = $this->checkPayment->checkPayment($this->parameters);

                            if ($this->setErrorMessages($checksWithRequest) === true) {
                                $subject->redirect(
                                    array(
                                        'controller' => 'checkout',
                                        'action' => 'shippingPayment',
                                        'sTarget' => 'checkout'
                                    )
                                );
                            } elseif ($checksWithRequest[0] === false) {
                                $subject->redirect(
                                    array(
                                        'controller' => 'checkout',
                                        'action' => 'shippingPayment',
                                        'sTarget' => 'checkout',
                                    )
                                );
                            }
                        }
                    } else {
                        if ($this->sessionManager->get('lastAction') === 'register/saveRegister') {
                            $subject->redirect(
                                array(
                                    'controller' => 'checkout',
                                    'action' => 'cart',
                                )
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Set correct error messages if there are errors.
     *
     * @param array $errorMessages
     * @param bool $update
     * @return bool
     * @throws Zend_Db_Adapter_Exception
     */
    private function setErrorMessages($errorMessages, $update = true)
    {
        $error = $this->getErrorMessages($errorMessages);
        $params[':errorMessage'] = $error['errorMessage'];

        $this->logger->debug(
            'Set error messages'
        );

        if ($error['payment'] === 'PAYOLUTION_INS') {
            $exits = $this->dbAdapter->fetchOne(
                'SELECT
                      COUNT(*)
                    FROM
                      bestit_payolution_installment
                    WHERE
                      userId = :userId',
                array(
                    ':userId' => $this->parameters[2]['additional']['user']['id'],
                )
            );

            if ($exits > 0) {
                $sql = $this->updateErrorInstallment;
            } else {
                $sql = '
                  INSERT INTO
                    bestit_payolution_installment
                      (`userId`,`errorMessage`)
                  VALUES
                    (:userId,:errorMessage)';
            }
        }

        if ($error['payment'] === 'PAYOLUTION_INVOICE_B2B' || $error['payment'] === 'PAYOLUTION_INVOICE_B2C') {
            $this->sessionManager->set('b2bErrorMessage', $error['errorMessage']);

            return $error['errorMessage'] !== '';
        }

        if (!empty($sql)) {
            if ($update === true && $params[':errorMessage'] !== '') {
                $params[':userId'] = $this->parameters[2]['additional']['user']['id'];
                $this->dbAdapter->query($sql, $params);
            }

            return ($params[':errorMessage'] !== '');
        }

        return false;
    }

    /**
     * Get error messages
     *
     * @param array $errorMessages
     * @return array
     */
    private function getErrorMessages($errorMessages)
    {
        $index = 0;
        $errorMessage = $payment = '';

        foreach ($errorMessages as $value) {
            if (!empty($value['message'])) {
                if ($index === 0) {
                    $errorMessage .= $value['message'];
                    $payment = $value['payment'];
                    $index = 1;
                } else {
                    $errorMessage .= ';'.$value['message'];
                }
            }
        }

        return array(
            'errorMessage' => $errorMessage,
            'payment' => $payment
        );
    }
}
