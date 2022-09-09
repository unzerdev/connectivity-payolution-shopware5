<?php

use Doctrine\ORM\NonUniqueResultException;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Util\Session\SessionManager;
use Shopware\Models\Order\Order;
use Doctrine\ORM\AbstractQuery;
use Payolution\BirthdayValidation\CheckBirthday;
use Payolution\Config\Config;
use Payolution\Request\B2B\RequestBuilder;
use Payolution\Request\Builder\RequestContext;
use Payolution\Request\Builder\RequestContextFactory;
use Payolution\Request\Builder\RequestOptions;
use Payolution\Request\ELV\ExecutePayment\ELVExecutePayment;
use Payolution\Request\ExecutePayment\CreatePostParams as ExecutePaymentCreatePostParam;
use Payolution\Request\ELV\ExecutePayment\CreatePostParams as ELVExecutePaymentCreatePostParam;
use Payolution\Request\ExecutePayment\ExecutePayment;
use Payolution\Request\Installment\Cl\GetJsLibrary;
use Payolution\Request\Installment\ExecutePayment\CreatePostParams as ExecutePaymentCreatePostParams;
use Payolution\Request\Installment\ExecutePayment\InstallmentExecutePayment;
use Payolution\Request\Installment\GetDocument;
use Payolution\Request\Installment\PreCheck\CreatePostParams as PreCheckCreatePostParams;
use Payolution\Request\Installment\PreCheck\InstallmentPreCheck;
use Payolution\Request\RequestWrapper;
use Payolution\Session\SessionTokenStorage;
use Payolution\Workflow\CaptureInvoker;
use PolPaymentPayolution\Config\PluginConfig;
use PolPaymentPayolution\GetPluginConfig;
use PolPaymentPayolution\Payment\PaymentInvoker;
use PolPaymentPayolution\Request\CreateRequestArray;
use PolPaymentPayolution\RiskManagement\CheckPersonal;
use Psr\Log\LoggerInterface;
use Shopware\Models\Shop\Shop;

/**
 * Frontend Payment Controller
 * Class Shopware_Controllers_Frontend_PolPaymentPayolution
 */
class Shopware_Controllers_Frontend_PolPaymentPayolution extends Shopware_Controllers_Frontend_Payment
{
    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $dbAdapter;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * @var Shopware_Components_Modules
     */
    private $modules;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippets;

    /**
     * @var GetJsLibrary
     */
    private $jsLibrary;

    /**
     * @var CreateRequestArray
     */
    private $requestArray;

    /**
     * @var RequestWrapper
     */
    private $requestWrapper;

    /**
     * @var CheckPersonal
     */
    private $checkPersonal;

    /**
     * @var PreCheckCreatePostParams
     */
    private $postParams;

    /**
     * @var GetDocument
     */
    private $documentRequest;

    /**
     * @var GetPluginConfig
     */
    private $getPluginConfig;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * @var SessionTokenStorage
     */
    private $sessionTokenStorage;

    /**
     * @var RequestContextFactory
     */
    private $requestContextFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PaymentInvoker
     */
    private $paymentInvoker;

    /**
     * @var RequestBuilder
     */
    private $b2bRequestBuilder;

    /**
     * @var ExecutePaymentCreatePostParams
     */
    private $installmentExecute;

    /**
     * @var CaptureInvoker
     */
    private $captureInvoker;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * Pre dispatch method
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->setDependencies();
    }

    /**
     * Index action to send the payment call to the right actions
     *
     * @return void
     *
     * @throws Exception
     */
    public function indexAction()
    {
        if ($this->sessionManager->get('payolutionOrderDone') > 0) {
            $this->redirect(['controller' => 'index', 'action' => 'index']);

            return;
        }

        switch ($this->getPaymentShortName()) {
            case 'payolution_invoice_b2c':
                $this->redirect(['action' => 'invoiceB2c', 'forceSecure' => true]);
                break;
            case 'payolution_invoice_b2b':
                $this->redirect(['action' => 'invoiceB2b', 'forceSecure' => true]);
                break;
            case 'payolution_installment':
                $this->redirect(['action' => 'installment', 'forceSecure' => true]);
                break;
            case 'payolution_elv':
                $this->redirect(['action' => 'elv', 'forceSecure' => true]);
                break;
            default:
                $this->redirect(['controller' => 'checkout']);
        }
    }

    /**
     * Action when an invoice for a normal customer is triggered.
     *
     * @return void
     *
     * @throws Exception
     */
    public function invoiceB2cAction()
    {
        if ($this->sessionManager->get('payolutionOrderDone') === 0) {
            $this->savePayolutionOrder($this->executePayment('PAYOLUTION_INVOICE'));
        } else {
            $this->redirect(array('controller' => 'index', 'action' => 'index'));
        }
    }

    /**
     * Action when an invoice for a business customer is triggered.
     *
     * @return void
     *
     * @throws Exception
     */
    public function invoiceB2bAction()
    {
        if ($this->sessionManager->get('payolutionOrderDone') === 0) {
            $this->savePayolutionOrder($this->executePayment('PAYOLUTION_INVOICE_B2B'));
        } else {
            $this->redirect(array('controller' => 'index', 'action' => 'index'));
        }
    }

    /**
     * Action when an installment payment is triggered.
     *
     * @return void
     *
     * @throws Exception
     */
    public function installmentAction()
    {
        if ($this->sessionManager->get('payolutionOrderDone') === 0) {
            $this->savePayolutionOrder($this->executePayment('PAYOLUTION_INS'));
        } else {
            $this->redirect(array('controller' => 'index', 'action' => 'index'));
        }
    }

    /**
     * Action when an elv payment is triggered.
     *
     * @return void
     *
     * @throws Exception
     */
    public function elvAction()
    {
        if ($this->sessionManager->get('payolutionOrderDone') === 0) {
            $this->savePayolutionOrder($this->executePayment('PAYOLUTION_ELV'));
        } else {
            $this->redirect(array('controller' => 'index', 'action' => 'index'));
        }
    }

    /**
     * Save Order and reauthorise
     *
     * @param array $payolutionData
     *
     * @return void
     *
     * @throws Zend_Db_Adapter_Exception
     * @throws NonUniqueResultException
     */
    public function savePayolutionOrder(array $payolutionData)
    {
        $user = $this->getUser();

        if (in_array($payolutionData['response']['PROCESSING_STATUS_CODE'], ['90', '00']) === true) {
            return $this->returnPositiveResult($payolutionData, $user);
        }

        return $this->returnNegativeResult($payolutionData, $user);
    }

    /**
     * Execute payment
     *
     * @param string $mode
     * @param bool|false $requestParams
     * @param RequestContext|null $context
     *
     * @return array
     *
     * @throws Exception
     */
    private function executePayment($mode, $requestParams = false, RequestContext $context = null)
    {
        // Do B2B Payment
        if ($mode === 'PAYOLUTION_INVOICE_B2B') {
            return $this->doB2bPayment($mode, $context);
        }

        if ($requestParams === false) {
            $requestParams = $this->getRequestParams($mode);
        }

        $data = $this->getDataBasedOnMode($mode);
        foreach ($requestParams as $method => $value) {
            $data->$method($value);
        }

        $payolutionConfigData = $this->dbAdapter->fetchPairs(
            'SELECT
              `name`,
              `value`
            FROM
              bestit_payolution_config
            WHERE
              shopId = :shopId
            AND
              currencyId = :currencyId',
            array(
                ':shopId' => $this->shop->getId(),
                ':currencyId' => $this->shop->getCurrency()->getId(),
            )
        );

        $payolutionConfig = new Config();
        $payolutionConfig->setConfig($payolutionConfigData);

        $postData = $this->getPostData($mode, $data, $payolutionConfig);

        return array(
            'request' => $requestParams,
            'response' => $this->requestWrapper->doRequest($postData),
            'payment' => $mode
        );
    }

    /**
     * Action for set birthday.
     *
     * @return void
     *
     * @throws Exception
     */
    public function setBirthdayAction()
    {
        $this->Front()->Plugins()->get('ViewRenderer')->setNoRender();
        $this->Response()->setHeader('Content-type', 'application/json', true);

        $birthday = $this->Request()->getParam('birthday');

        if (CheckBirthday::validateBirthday($birthday) === false) {
            $this->Response()->setBody(json_encode(['success' => false]));

            return;
        }

        $userId = $this->sessionManager->get('sUserId');
        //TODO: refactor CheckPersonal::setBirthday
        $this->checkPersonal->setBirthday($birthday, $userId);

        $this->Response()->setBody(json_encode(['success' => true]));
    }

    /**
     * Adds installment information to template.
     *
     * @return void
     *
     * @throws Exception
     */
    public function installmentInformationAction()
    {
        $this->View()->assign(
            'payolutionInstallmentArray',
            json_decode($this->sessionManager->get('payolutionInstallmentArray'))
        );
    }

    /**
     * Work to do before installment check.
     *
     * @return void
     *
     * @throws Enlight_Event_Exception
     * @throws Enlight_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function installmentCheckoutPreCheckAction()
    {
        $this->View()->setTemplate();
        $clId = $this->dbAdapter->fetchOne(
            'SELECT
              clId
            FROM
              bestit_payolution_installment
            WHERE
              userId = :userId',
            array(
                ':userId' => $this->sessionManager->get('sUserId')
            )
        );

        $basket = $this->modules->Basket()->sGetBasket();
        $user = $this->modules->Admin()->sGetUserData();

        $this->sessionManager->set('payolutionSkipRiskManagement', true);
        $shippingcosts = $this->modules->Admin()->sGetPremiumShippingcosts();
        $this->sessionManager->set('payolutionSkipRiskManagement', false);

        $basket['AmountNumeric'] += $shippingcosts['brutto'];
        $basket['AmountNetNumeric'] += $shippingcosts['netto'];
        if (!empty($basket['AmountWithTaxNumeric']) && isset($basket['AmountWithTaxNumeric'])) {
            $basket['AmountWithTaxNumeric'] += $shippingcosts['brutto'];
        }

        $taxFree = (int)$this->dbAdapter->fetchOne(
            'SELECT
                  taxfree
                FROM
                  s_core_countries
                WHERE
                  id = :countryId',
            array(
                ':countryId' => $user['additional']['country']['id']
            )
        );

        if ($taxFree === 1) {
            $basket['AmountNumeric'] = $basket['AmountNetNumeric'];
        }

        $requestParams = $this->requestArray->createArray($basket, $user, true, 'PAYOLUTION_INS', $taxFree);
        $requestParams['setCRITERIONPAYOLUTIONCALCULATIONID'] = $clId;

        $data = new InstallmentPreCheck();

        foreach ($requestParams as $method => $value) {
            $data->$method($value);
        }

        $postData = $this->postParams->createParams($data);
        $return = $this->requestWrapper->doRequest($postData);

        if (in_array($return['PROCESSING_STATUS_CODE'], ['00', '90'])) {
            $this->dbAdapter->query(
                'UPDATE
                  bestit_payolution_installment
                SET
                  pcId = :pcId
                WHERE
                  userId = :userId',
                array(
                    ':pcId' => $return['IDENTIFICATION_UNIQUEID'],
                    ':userId' => $user['additional']['user']['id']
                )
            );

            echo 'true';
        } else {
            $this->dbAdapter->query(
                'UPDATE
                  bestit_payolution_installment
                SET
                  errorMessage = "rejected"
                WHERE
                  userId = :userId',
                array(
                    ':userId' => $user['additional']['user']['id']
                )
            );

            echo 'false';
        }
    }

    /**
     * Action when a pdf for elv payment is needed.
     *
     * @return void
     *
     * @throws Exception
     */
    public function getInstallmentPdfAction()
    {
        $this->View()->setTemplate();

        $duration = $this->Request()->getParam('duration');
        $userId = $this->sessionManager->get('sUserId');

        if (!$userId) {
            $this->redirect('/');

            return;
        }

        $payolutionData = $this->dbAdapter->fetchOne(
            'SELECT
              request
            FROM
              bestit_payolution_installment
            WHERE
              userId = :userId',
            [
                ':userId' => $userId
            ]
        );

        $payolutionData = json_decode($payolutionData);

        $link = '';
        foreach ($payolutionData->PaymentDetails as $durations) {
            if ($durations->Duration == $duration) {
                $link = $durations->StandardCreditInformationUrl;
                break;
            }
        }

        $filename = $this->snippets->getNamespace('frontend/pol_payment_payolution/installment_checkout')
            ->get('payolutionInstallmentPdfName', 'Ratenplan', true);
        $file = $this->documentRequest->doRequest($link);
        header('Content-Disposition: attachment; filename='. $filename .'-'. $duration .'.pdf');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Description: File Transfer');
        header('Content-Length: '. filesize($file));
        echo $file;

        exit;
    }

    /**
     * Output of js file content.
     *
     * @return void
     */
    public function getClJsLibraryAction()
    {
        $this->View()->setTemplate();
        $file = Shopware()->DocPath() . 'files/payolution/jsClLibrary.js';

        if (!file_exists($file)) {
            $this->jsLibrary->getLibrary($file);
        }

        echo file_get_contents($file);

        die();
    }

    /**
     * Get Tax Free Value of Country
     *
     * @param int $id
     *
     * @return string
     *
     * @throws Exception
     */
    private function getCountryTaxFree($id)
    {
        return $this->dbAdapter->fetchOne(
            'SELECT
                  taxfree
                FROM
                  s_core_countries
                WHERE
                  id = :countryId',
            array(
                ':countryId' => $id
            )
        );
    }

    /**
     * Return data when request succeeded.
     *
     * @param array $payolutionData
     * @param array $user
     *
     * @return array
     *
     * @throws Zend_Db_Adapter_Exception
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function returnPositiveResult($payolutionData, $user)
    {
        $orderstate = null;
        $config = $this->getPluginConfig->getPluginConfig();

        if ($payolutionData['payment'] === 'PAYOLUTION_INS') {
            $orderstate = $config['installmentOrderState'];
        } elseif ($payolutionData['payment'] === 'PAYOLUTION_INVOICE') {
            $orderstate = $config['b2cOrderState'];
        } elseif ($payolutionData['payment'] === 'PAYOLUTION_ELV') {
            $orderstate = $config['elvOrderState'];
        } elseif ($payolutionData['payment'] === 'PAYOLUTION_INVOICE_B2B') {
            $orderstate = $config['b2bOrderState'];
        }

        $checkUserAttributes = $this->dbAdapter->fetchOne(
            'SELECT
                  id
                FROM
                  s_user_attributes
                WHERE
                  userID = :userId',
            array(
                ':userId' => $user['additional']['user']['id'],
            )
        );

        if (!empty($checkUserAttributes)) {
            $this->dbAdapter->query(
                'UPDATE
                      s_user_attributes sua
                    SET
                      sua.payolution_payment_reference_id_temp = :paymentReferenceId
                    WHERE
                      sua.userId = :userId',
                array(
                    ':userId' => $user['additional']['user']['id'],
                    ':paymentReferenceId' => $payolutionData['response']['PROCESSING_CONNECTORDETAIL_PaymentReference'],
                )
            );
        } else {
            $this->dbAdapter->query(
                'INSERT INTO
                      s_user_attributes
                      (`userID`,`payolution_payment_reference_id_temp`)
                    VALUES
                      (:userId,:paymentReferenceId)',
                array(
                    ':userId' => $user['additional']['user']['id'],
                    ':paymentReferenceId' => $payolutionData['response']['PROCESSING_CONNECTORDETAIL_PaymentReference'],
                )
            );
        }

        $orderNumber = $this->saveOrder(
            $payolutionData['response']['IDENTIFICATION_TRANSACTIONID'],
            $payolutionData['response']['IDENTIFICATION_UNIQUEID'],
            $orderstate
        );

        $queryBuilder = $this->componentManager->getModelManager()->createQueryBuilder();

        $queryBuilder->from(Order::class, 'o')
            ->select('o.id')
            ->where($queryBuilder->expr()->eq('o.number', ':number'))
            ->setParameter('number', $orderNumber);

        $orderId = $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        $this->sessionTokenStorage->saveSessionToken($orderId);

        $paymentType = null;
        if (isset($payolutionData['payment'])) {
            $paymentType = $payolutionData['payment'];
        }

        $context = null;
        if (isset($payolutionData['context'])) {
            /**
             * @var RequestContext $oldContext
             */
            $oldContext = $payolutionData['context'];

            $context = $this->requestContextFactory->createWithTransactionInfos(
                $orderNumber,
                $oldContext->getTrxId(),
                $payolutionData['response']['IDENTIFICATION_UNIQUEID']
            );
        } else {
            $payolutionData['request']['setIDENTIFICATIONREFERENCEID'] =
                $payolutionData['response']['IDENTIFICATION_UNIQUEID'];
            $payolutionData['request']['setIDENTIFICATIONTRANSACTIONID'] = $orderNumber;
        }

        $response = $this->executePayment(
            $paymentType,
            $payolutionData['request'],
            $context
        );

        $this->dbAdapter->query(
            'UPDATE
                  s_order_attributes soa
                INNER JOIN
                  s_order so
                ON
                  so.id = soa.orderId
                SET
                  soa.payolution_unique_id = :uniqueId,
                  soa.payolution_payment_reference_id = :paymentReferenceId
                WHERE
                  so.ordernumber = :ordernumber',
            array(
                ':uniqueId' => $response['response']['IDENTIFICATION_UNIQUEID'],
                ':paymentReferenceId' => $payolutionData['response']['PROCESSING_CONNECTORDETAIL_PaymentReference'],
                ':ordernumber' => $orderNumber,
            )
        );

        $this->logger->info('Check Auto Capture');
        if ($payolutionData['payment'] === 'PAYOLUTION_INS' || $this->pluginConfig->isAutomaticCaptureOrders()) {
            $this->logger->info('Init Auto Capture');
            if ($orderId) {
                $this->captureInvoker->invokeCaptureWholeOrder($orderId);
            }

            if ($payolutionData['payment'] === 'PAYOLUTION_INS') {
                $sql = '
                  DELETE FROM
                    bestit_payolution_installment
                  WHERE
                    userId = :userId';

                $this->dbAdapter->query($sql, [':userId' => $user['additional']['user']['id']]);
            }
        }

        $this->paymentInvoker->invokeSuccessfulPayment($orderNumber);

        return $this->redirect([
            'controller' => 'checkout',
            'action' => 'finish',
            'sUniqueID' => $payolutionData['response']['IDENTIFICATION_UNIQUEID']
        ]);
    }

    /**
     * Return data when request failed.
     *
     * @param array $payolutionData
     * @param array $user
     *
     * @throws Zend_Db_Adapter_Exception
     * @throws Exception
     */
    private function returnNegativeResult($payolutionData, $user)
    {
        if ($payolutionData['payment'] === 'PAYOLUTION_INS') {
            $sql = '
              UPDATE
                bestit_payolution_installment
              SET
                errorMessage = :errorMessage,
                accountIban = NULL
              WHERE
                userId = :userId';

            $this->dbAdapter->query($sql, [':errorMessage' => 'rejected', ':userId' => $user['additional']['user']['id']]);
        }

        $this->sessionManager->set('payolutionErrorMessage', 'rejected');

        return $this->redirect([
            'controller' => 'checkout',
            'action' => 'shippingPayment',
            'sTarget' => 'checkout'
        ]);
    }

    /**
     * Do a b2b payment.
     *
     * @param string $mode
     * @param RequestContext $context
     *
     * @return array
     *
     * @throws Exception
     */
    private function doB2bPayment($mode, $context)
    {
        $user = $this->getUser();
        $basket = $this->getBasket();

        if (!$context) {
            $context = $this->requestContextFactory->create();
        }
        $taxFree = $this->getCountryTaxFree($user['additional']['country']['id']);
        $requestOptions = new RequestOptions(
            $basket,
            $user,
            filter_var($taxFree, FILTER_VALIDATE_BOOLEAN)
        );

        $b2bRequestParams = $this->b2bRequestBuilder->buildRequest($requestOptions, $context);

        return [
            'request' => $b2bRequestParams,
            'response' => $this->requestWrapper->doRequest($b2bRequestParams),
            'payment' => $mode,
            'context' => $context
        ];
    }

    /**
     * Get the correct execution payment object based on payment mode.
     *
     * @param string $mode
     *
     * @return ELVExecutePayment|ExecutePayment|InstallmentExecutePayment
     */
    private function getDataBasedOnMode($mode)
    {
        if ($mode === 'PAYOLUTION_INS') {
            return new InstallmentExecutePayment();
        }

        if ($mode === 'PAYOLUTION_ELV') {
            return new ELVExecutePayment();
        }

        return new ExecutePayment();
    }

    /**
     * Generate request params when none were given.
     *
     * @param string $mode
     *
     * @return array
     *
     * @throws Exception
     */
    private function getRequestParams($mode)
    {
        $user = $this->getUser();
        $taxFree = (int)$this->getCountryTaxFree($user['additional']['country']['id']);
        $basket = $this->getBasket();

        if ($taxFree === 1) {
            $basket['AmountNumeric'] = $basket['AmountNetNumeric'];
        }

        if ($mode === 'PAYOLUTION_ELV') {
            $user['payolution_elv'] = $this->dbAdapter->fetchRow(
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
        }

        $requestParams = $this->requestArray->createArray($basket, $user, false, $mode, $taxFree);
        $requestParams['setIDENTIFICATIONTRANSACTIONID'] = $this->createPaymentUniqueId();
        $requestParams['setPRESENTATIONUSAGE'] = 'Trx ' . $this->createPaymentUniqueId();

        return $requestParams;
    }

    /**
     * Get the correct post data for the payment.
     *
     * @param string $mode
     * @param $data
     * @param Config $payolutionConfig
     *
     * @return array
     */
    private function getPostData($mode, $data, $payolutionConfig)
    {
        if ($mode === 'PAYOLUTION_INS') {
            return $this->installmentExecute->createParams($data);
        }

        if ($mode === 'PAYOLUTION_ELV') {
            return ELVExecutePaymentCreatePostParam::createParams($data, $payolutionConfig);
        }

        return ExecutePaymentCreatePostParam::createParams($data, $payolutionConfig);
    }

    /**
     * Sets dependencies to other services. Not possible in init() function because container is null then.
     * Another idea: when in future SW versions controller dependencies could be set through services.xml or
     * any other method this function can be removed easily.
     *
     * @return void
     */
    private function setDependencies()
    {
        $this->dbAdapter = $this->container->get('db');
        $this->modules = $this->container->get('modules');
        $this->snippets = $this->container->get('snippets');
        $this->jsLibrary = $this->container->get('payolution.get_js_cl_library');
        $this->requestArray = $this->container->get('payolution.create_request_array_from_shopware');
        $this->requestWrapper = $this->container->get('payolution.request.request_wrapper');
        $this->checkPersonal = $this->container->get('payolution.check_personal');
        $this->postParams = $this->container->get('payolution.create_post_params_installment_pc');
        $this->documentRequest = $this->container->get('payolution.get_document_request');
        $this->getPluginConfig = $this->container->get('payolution.get_plugin_config');
        $this->pluginConfig = $this->container->get('payolution.plugin_config');
        $this->sessionTokenStorage = $this->container->get('payolution.session.session_token_storage');
        $this->requestContextFactory = $this->container->get('payolution.request.builder.request_context_factory');
        $this->logger = $this->container->get('pol_payment_payolution.plugin_logger');
        $this->paymentInvoker = $this->container->get('pol_payment_payolution.payment.payment_invoker');
        $this->b2bRequestBuilder = $this->container->get('payolution.request.b2b.request_builder');
        $this->installmentExecute = $this->container->get('payolution.create_post_params_installment_execute');
        $this->captureInvoker = $this->container->get('payolution.workflow.capture_invoker');
        $this->componentManager = $this->container->get('pol_payment_payolution.component_manager.component_manager');
        $this->sessionManager = $this->container->get('pol_payment_payolution.util.session.session_manager');

        if ($this->container->initialized('shop')) {
            $this->shop = $this->container->get('shop');
        }

        if ($this->shop === null) {
            $this->shop = $this->container->get('models')->getRepository(Shop::class)->getActiveDefault();
        }
    }
}
