<?php

namespace PolPaymentPayolution\Subscriber;

use Doctrine\DBAL\DBALException;
use Enlight\Event\SubscriberInterface;
use Enlight_Hook_HookArgs;
use Exception;
use Payolution\Config\ConfigLoader;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Locale;
use Shopware\Models\Shop\Shop;
use Shopware_Components_Document;
use Shopware_Components_Snippet_Manager;
use Shopware_Components_Translation;

/**
 * Subscriber to handle all documents
 *
 * @package PolPaymentPayolution\Subscriber
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class DocumentSubscriber implements LoggerAwareInterface, SubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * Whitelist for the allowed payments
     *
     * @var array
     */
    const PAYMENT_WHITELIST = [
        'payolution_invoice_b2c',
        'payolution_invoice_b2b',
        'payolution_elv'
    ];

    /**
     * @var Shopware_Components_Translation
     */
    private $translator;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippets;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * DocumentSubscriber constructor.
     *
     * @param Shopware_Components_Translation $translator
     * @param ModelManager $modelManager
     * @param Shopware_Components_Snippet_Manager $snippets
     * @param ConfigLoader $configLoader
     */
    public function __construct(
        Shopware_Components_Translation $translator,
        ModelManager $modelManager,
        Shopware_Components_Snippet_Manager $snippets,
        ConfigLoader $configLoader
    ) {
        $this->translator = $translator;
        $this->modelManager = $modelManager;
        $this->snippets = $snippets;
        $this->configLoader = $configLoader;

        $this->logger = new NullLogger();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @inheritdoc
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Components_Document::assignValues::after' => 'onBeforeRenderDocument'
        ];
    }

    /**
     * add variables to document.
     *
     * @param Enlight_Hook_HookArgs $args
     *
     * @throws Exception
     */
    public function onBeforeRenderDocument(Enlight_Hook_HookArgs $args)
    {
        /**
         * @var Shopware_Components_Document $document
         */
        $document = $args->getSubject();
        $orderId = $document->_order->id;

        $order = $document->_order;
        $payloadOrder = $document->_order->__toArray();
        $this->logger->info('Attach variables to document template');

        $payloadOrderId = isset($payloadOrder['_id']) ? $payloadOrder['_id'] : null;

        $paymentName = $order->payment['name'];

        if ($payloadOrderId && $orderId && in_array($paymentName, self::PAYMENT_WHITELIST)) {
            $view = $document->_view;
            $shopId = $this->getShopIdFromDocument($document);

            $paymentInstruction = $this->getDocumentData($orderId, $shopId);
            $document->_template->assign('instruction', $paymentInstruction);

            /** @var array $containerData */
            $containerData = $view->getTemplateVars('Containers');

            $this->attachContainerData($containerData, $paymentName, $document);

            $view->assign('Containers', $containerData);
            $this->setInvoiceId($payloadOrderId);
        }
    }

    /**
     * Set the invoice id in the order attributes
     *
     * @param int $orderId
     *
     * @return void
     *
     * @throws DBALException
     */
    private function setInvoiceId($orderId)
    {
        $connection = $this->modelManager->getConnection();

        $fetchSql = <<<sql
SELECT docID FROM s_order_documents WHERE orderID  = :id
sql;

        $invoiceId = $connection->fetchColumn($fetchSql, [':id' => $orderId]);

        $updateSql = <<<sql
UPDATE s_order_attributes SET payolution_invoice_id = :invoiceId WHERE orderID = :orderOd
sql;

        $connection->executeQuery($updateSql, [
            ':invoiceId' => $invoiceId,
            ':orderOd' => $orderId,
        ]);
    }

    /**
     * Get Payolution document container
     *
     * @param array $translation
     *
     * @return array
     */
    private function getPayolutionContainerData(array $translation)
    {
        $containers = $this->getDocumentContainers();

        $payolutionData = [];

        foreach ($containers as $key => $container) {
            if (!is_numeric($key) || !count($container)) {
                $this->logger->error('Corrupt document container given', ['key' => $key, 'container' => $container]);
                continue;
            }

            if (!empty($translation[1][$container['name'] . '_Value'])) {
                $container['value'] = $translation[1][$container['name'] . '_Value'];
            }
            if (!empty($translation[1][$container['name'] . '_Style'])) {
                $container['style'] = $translation[1][$container['name'] . '_Style'];
            }

            $payolutionData[$container['name']] = $container;
        }

        return $payolutionData;
    }

    /**
     * Attach payolution data to given document container
     *
     * @param array $containerData
     * @param string $paymentName
     * @param Shopware_Components_Document $document
     *
     * @return void
     *
     * @throws Exception
     */
    private function attachContainerData(array &$containerData, $paymentName, Shopware_Components_Document $document)
    {
        $translation = $this->translator->read($this->getShopIdFromDocument($document), 'documents', 1);

        $payolutionData = $this->getPayolutionContainerData($translation);

        if ($paymentName !== 'payolution_elv') {
            $templateManager = $document->_template;

            $containerData['Content_Info'] = $payolutionData['payolution_invoice_template'];
            $containerData['Content_Info']['value'] = $templateManager->fetch(
                'string:' . $containerData['Content_Info']['value']
            );
            $containerData['Content_Info']['style'] = str_replace('\r\n', '', $containerData['Content_Info']['style']);
        } else {
            $locale = $this->getLocaleByShopId($this->getShopIdFromDocument($document));
            $snippet = $this->snippets
                ->setLocale($locale)
                ->getNamespace('frontend/pol_payment_payolution/elv_document')
                ->get(
                    'payolutionElvDocumentText',
                    '<p>Bei Teillieferungen erfolgt die Abbuchung in Teilbetr&auml;gen.</p>',
                    true
                );

            $containerData['Content_Info']['value'] = $snippet . $containerData['Content_Info']['value'];
        }

        $containerData['Footer'] = $payolutionData['payolution_invoice_template_footer'];
        $containerData['Footer']['value'] = $document->_template->fetch(
            'string:' . $containerData['Footer']['value']
        );
        $containerData['Footer']['style'] = str_replace('\r\n', '', $containerData['Footer']['style']);
    }

    /**
     * Get locale model by shop id
     *
     * @param int $shopId
     *
     * @return Locale|null
     *
     * @throws Exception
     */
    private function getLocaleByShopId($shopId)
    {
        $locale = null;

        /** @var Shop $shop */
        if ($shop = $this->modelManager->getRepository(Shop::class)->find($shopId)) {
            $locale = $shop->getLocale();
        }

        return $locale;
    }

    /**
     * Get the payolution document container
     *
     * @return array
     */
    private function getDocumentContainers()
    {
        $fetchSql = <<<sql
SELECT *
FROM s_core_documents_box
WHERE documentID = :docId
  AND (name = :payolutionInfo OR name = :payolutionFooter)
sql;
        return $this->modelManager->getConnection()->fetchAll(
            $fetchSql,
            [
                ':docId' => 1,
                ':payolutionInfo' => 'payolution_invoice_template',
                ':payolutionFooter' => 'payolution_invoice_template_footer',
            ]
        );
    }

    /**
     * Get the shop id from document
     *
     * @param Shopware_Components_Document $document
     *
     * @return int
     */
    private function getShopIdFromDocument(Shopware_Components_Document $document)
    {
        return $document->_order->order->language;
    }

    /**
     * Get specific document data for the given order id
     *
     * @param int $orderId
     * @param int $shopId
     *
     * @return array
     */
    private function getDocumentData($orderId, $shopId)
    {
        $sql = <<<sql
SELECT `payolution_payment_reference_id`
FROM s_order_attributes
WHERE orderID = :orderId
sql;

        $paymentReferenceId = $this->modelManager->getConnection()->fetchColumn($sql, [':orderId' => $orderId]);

        $config = $this->configLoader->loadConfigByShop($shopId);

        return [
            'recipient' => $config->getHolder(),
            'iban' => $config->getIban(),
            'bic' => $config->getBic(),
            'reference' => $paymentReferenceId
        ];
    }
}