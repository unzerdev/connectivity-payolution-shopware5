<?php

namespace PolPaymentPayolution\Setup;

use Doctrine\DBAL\DBALException;
use Enlight_Exception;
use Exception;
use PolPaymentPayolution\Plugin\MetaDataExtractor;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\PaymentInstaller;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Locale;
use Shopware_Components_Translation;

/**
 * The installer handles the install process for the plugin
 *
 * @package PolPaymentPayolution\Setup
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class Installer
{
    /**
     * Mapping for the payment translations
     *
     * @var array
     */
    const PAYMENT_TRANSLATIONS = [
        'nl_NL' => [
            'payolution_invoice_b2c' => 'Kopen op factuur',
            'payolution_invoice_b2b' => 'Bedrijfsfactuur',
            'payolution_installment' => 'Koop met termijnbetaling',
            'payolution_elv' => 'Incasso'
        ]
    ];

    /**
     * @var PaymentInstaller
     */
    private $paymentInstaller;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var CrudService
     */
    private $crudService;

    /**
     * @var Shopware_Components_Translation
     */
    private $translator;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * Installer constructor.
     *
     * @param PaymentInstaller $paymentInstaller
     * @param ModelManager $modelManager
     * @param CrudService $crudService
     * @param Shopware_Components_Translation $translator
     * @param string $pluginName
     * @param string $pluginDirectory
     */
    public function __construct(
        PaymentInstaller $paymentInstaller,
        ModelManager $modelManager,
        CrudService $crudService,
        Shopware_Components_Translation $translator,
        $pluginName,
        $pluginDirectory
    ) {
        $this->paymentInstaller = $paymentInstaller;
        $this->modelManager = $modelManager;
        $this->crudService = $crudService;
        $this->translator = $translator;
        $this->pluginName = $pluginName;
        $this->pluginDirectory = $pluginDirectory;
    }

    /**
     * Sync plugin config
     *
     * @param string $version
     *
     * @return void
     */
    public function syncPluginConfig($version)
    {
        if (version_compare($version, '5.0.7', '<')) {
            $migrateSql = <<<sql
REPLACE INTO `bestit_payolution_config`
  (`shopId`, `currencyId`, `name`, `value`)
VALUES (1, 1, 'allowedCountriesInvoiceB2C', 'DE,AT,CH'),
       (1, 1, 'allowedCountriesInvoiceB2B', 'DE,AT,CH'),
       (1, 1, 'allowedCountriesInstallment', 'DE,AT'),
       (1, 1, 'allowedCountriesElv', 'DE,AT'),
       (1, 1, 'min_elv_value', '1'),
       (1, 1, 'max_elv_value', '999999');
DELETE
FROM `bestit_payolution_config`
WHERE `name` = 'allowedCountries';
DELETE
FROM `bestit_payolution_config_order`
WHERE `name` = 'allowedCountries';
sql;
            $this->modelManager->getConnection()->executeQuery($migrateSql);
        }
    }

    /**
     * Removes config values with invalid shop id
     *
     * @return void
     */
    public function removeOrphanedSubshopConfigValues()
    {
        $connection = $this->modelManager->getConnection();
        $fetchIdsSql = <<<sql
SELECT DISTINCT payo.shopId
FROM bestit_payolution_config payo
WHERE payo.shopId NOT IN (SELECT id FROM s_core_shops)
sql;
        $shopIds = $connection->fetchAll($fetchIdsSql);
        foreach (array_column($shopIds, 'shopId') as $shopId) {
            $connection->executeQuery(
                'DELETE FROM bestit_payolution_config WHERE shopId= :shopId',
                [
                    ':shopId' => $shopId
                ]
            );
        }
    }

    /**
     * Removes config values with invalid currency id
     *
     * @return void
     */
    public function removeOrphanedCurrencyConfigValues()
    {
        $connection = $this->modelManager->getConnection();
        $fetchIdsSql = <<<sql
SELECT DISTINCT payo.currencyId
FROM bestit_payolution_config payo
WHERE payo.currencyId NOT IN (SELECT id FROM s_core_currencies)
sql;
        $currencyIds = $connection->fetchAll($fetchIdsSql);
        foreach (array_column($currencyIds, 'currencyId') as $currencyId) {
            $connection->executeQuery(
                'DELETE FROM bestit_payolution_config WHERE currencyId= :currencyId',
                [
                    ':currencyId' => $currencyId
                ]
            );
        }
    }

    /**
     * Synchronize the plugin with the shops
     *
     * @return void
     *
     * @throws Exception This exception is thrown if an error in the process is thrown
     */
    public function syncPlugin()
    {
        $this->createPayments();
        $this->installOrUpdateDocuments();
        $this->modifySchema();
        $this->createTables();
        $this->addAttributes();
    }

    /**
     * Deactivate all payolution payments
     *
     * @return void
     *
     * @throws DBALException
     */
    public function deactivatePayments()
    {
        $sql = <<<sql
SELECT scm.name, scm.id
FROM s_core_paymentmeans scm
       INNER JOIN s_core_plugins scp ON scm.pluginID = scp.id
WHERE scp.name = :name
sql;
        $this->modelManager->getConnection()->executeQuery($sql, [':name' => $this->pluginName]);
    }

    /**
     * Install or update the shopware documents
     *
     * @return void
     *
     * @throws Exception
     */
    private function installOrUpdateDocuments()
    {
        $templateInvoiceId = $this->getIdOfDocument('payolution_invoice_template');
        $templateInvoiceContent = file_get_contents(
            $this->pluginDirectory . DIRECTORY_SEPARATOR . 'Resources/assets/document_bill_translation_de_DE.txt'
        );

        if ($templateInvoiceId === null) {
            $this->createShopwareDocument($templateInvoiceContent, '', 'payolution_invoice_template');
        } else {
            $this->updateShopwareDocument($templateInvoiceId, $templateInvoiceContent, '');
        }

        $templateFooterId = $this->getIdOfDocument('payolution_invoice_template_footer');
        $templateFooterContent = file_get_contents(
            $this->pluginDirectory . DIRECTORY_SEPARATOR . 'Resources/assets/document_bill_footer_translation_de_DE.txt'
        );
        $templateFooterStyle = 'width: 170mm;\r\nposition:fixed;\r\nbottom:-20mm;\r\nheight: 15mm;';

        if ($templateFooterId === null) {
            $this->createShopwareDocument($templateFooterContent, $templateFooterStyle, 'payolution_invoice_template_footer');
        } else {
            $this->updateShopwareDocument($templateFooterId, $templateFooterContent, $templateFooterStyle);
        }

        $this->updateTranslation(
            'nl_NL',
            'documents',
            $this->getIdOfDocument('payolution_invoice_template'),
            [
                'value' => file_get_contents(
                    $this->pluginDirectory . DIRECTORY_SEPARATOR . 'Resources/assets/document_bill_translation_nl_NL.txt'
                )
            ]
        );
    }

    /**
     * Create th db tables
     *
     * @return void
     *
     * @throws DBALException
     */
    private function createTables()
    {
        $connection = $this->modelManager->getConnection();

        $connection->query(
            'DROP TABLE IF EXISTS bestit_payolution_userCheck;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_installment (
                `userId` int(11) NOT NULL,
                `clId` varchar(255) DEFAULT NULL,
                `pcId` varchar(255) DEFAULT NULL,
                `amount` varchar(255) DEFAULT NULL,
                `financeAmount` varchar(255) DEFAULT NULL,
                `currency` varchar(255) DEFAULT NULL,
                `duration` varchar(255) DEFAULT NULL,
                `accountHolder` varchar(255) DEFAULT NULL,
                `accountCountry` varchar(255) DEFAULT NULL,
                `accountBic` varchar(255) DEFAULT NULL,
                `accountIban` varchar(255) DEFAULT NULL,
                `errorMessage` text COLLATE utf8_unicode_ci,
                `request` text COLLATE utf8_unicode_ci,
                PRIMARY KEY (`userId`),
                FOREIGN KEY (`userId`) REFERENCES s_user(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_config (
                `shopId` int(11) NOT NULL,
                `currencyId` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `value` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`shopId`,`currencyId`,`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_cr_log (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `orderId` int(11) NOT NULL,
                `date` DATETIME DEFAULT NULL,
                `articlename` varchar(255) DEFAULT NULL,
                `quantity` int(11) DEFAULT NULL,
                `amount` varchar(255) DEFAULT NULL,
                `type` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_elv (
                `userId` int(11) NOT NULL,
                `accountHolder` varchar(255) DEFAULT NULL,
                `accountBic` varchar(255) DEFAULT NULL,
                `accountIban` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`userId`),
                FOREIGN KEY (`userId`) REFERENCES s_user(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_b2b (
                `type` varchar(255) DEFAULT NULL,
                `userId` int(11) NOT NULL,
                `company` varchar(255) DEFAULT NULL,
                `vat` varchar(255) DEFAULT NULL,
                `firstName` varchar(255) DEFAULT NULL,
                `lastName` varchar(255) DEFAULT NULL,
                `birthday` DATETIME DEFAULT NULL,
                PRIMARY KEY (`userId`),
                FOREIGN KEY (`userId`) REFERENCES s_user(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_installment (
                `userId` int(11) NOT NULL,
                `clId` varchar(255) DEFAULT NULL,
                `pcId` varchar(255) DEFAULT NULL,
                `amount` varchar(255) DEFAULT NULL,
                `financeAmount` varchar(255) DEFAULT NULL,
                `currency` varchar(255) DEFAULT NULL,
                `duration` varchar(255) DEFAULT NULL,
                `accountHolder` varchar(255) DEFAULT NULL,
                `accountCountry` varchar(255) DEFAULT NULL,
                `accountBic` varchar(255) DEFAULT NULL,
                `accountIban` varchar(255) DEFAULT NULL,
                `errorMessage` text COLLATE utf8_unicode_ci,
                `request` text COLLATE utf8_unicode_ci,
                PRIMARY KEY (`userId`),
                FOREIGN KEY (`userId`) REFERENCES s_user(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_config (
                `shopId` int(11) NOT NULL,
                `currencyId` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `value` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`shopId`,`currencyId`,`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_cr_log (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `orderId` int(11) NOT NULL,
                `date` DATETIME DEFAULT NULL,
                `articlename` varchar(255) DEFAULT NULL,
                `quantity` int(11) DEFAULT NULL,
                `amount` varchar(255) DEFAULT NULL,
                `type` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_elv (
                `userId` int(11) NOT NULL,
                `accountHolder` varchar(255) DEFAULT NULL,
                `accountBic` varchar(255) DEFAULT NULL,
                `accountIban` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`userId`),
                FOREIGN KEY (`userId`) REFERENCES s_user(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS bestit_payolution_b2b (
                `type` varchar(255) DEFAULT NULL,
                `userId` int(11) NOT NULL,
                `company` varchar(255) DEFAULT NULL,
                `vat` varchar(255) DEFAULT NULL,
                `firstName` varchar(255) DEFAULT NULL,
                `lastName` varchar(255) DEFAULT NULL,
                `birthday` DATETIME DEFAULT NULL,
                PRIMARY KEY (`userId`),
                FOREIGN KEY (`userId`) REFERENCES s_user(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        $connection->query(
            'CREATE TABLE IF NOT EXISTS `bestit_payolution_config_order` (
              `name` varchar(255) NOT NULL,
              `order` smallint(6) DEFAULT NULL,
              PRIMARY KEY (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        // Default data
        $connection->query("
            INSERT INTO `bestit_payolution_config` (`shopId`, `currencyId`, `name`, `value`) VALUES
                (1, 1, 'bic', 'BIC: Diese erhalten Sie von payolution GmbH'),
                (1, 1, 'channel_b2b_invoice', 'gateway'),
                (1, 1, 'channel_installment', 'gateway'),
                (1, 1, 'channel_invoice', 'gateway'),
                (1, 1, 'channel_elv', 'gateway'),
                (1, 1, 'channel_precheck', 'gateway'),
                (1, 1, 'iban', 'IBAN: Diese erhalten Sie von payolution GmbH'),
                (1, 1, 'logging', 'true'),
                (1, 1, 'login', 'gateway'),
                (1, 1, 'mail_bcc', 'invoices@payolution.com'),
                (1, 1, 'passwd', '123dabei'),
                (1, 1, 'sender', 'gateway'),
                (1, 1, 'testmode', 'true'),
                (1, 1, 'min_installment_value', '1'),
                (1, 1, 'installment_payolution_user', 'gateway-installment'),
                (1, 1, 'installment_payolution_password', '123dabei'),
                (1, 1, 'max_installment_value', '999999'),
                (1, 1, 'min_b2b_value', '1'),
                (1, 1, 'max_b2b_value', '999999'),
                (1, 1, 'min_b2c_value', '1'),
                (1, 1, 'max_b2c_value', '999999'),
                (1, 1, 'min_elv_value', '1'),
                (1, 1, 'max_elv_value', '999999'),
                (1, 1, 'min_installment_detail_value', '1'),
                (1, 1, 'max_installment_detail_value', '999999'),
                (1, 1, 'differentAddresses', 'true'),
                (1, 1, 'allowedCountriesInvoiceB2C', 'DE,AT,CH'),
                (1, 1, 'allowedCountriesInvoiceB2B', 'DE,AT,CH'),
                (1, 1, 'allowedCountriesInstallment', 'DE,AT'),
                (1, 1, 'allowedCountriesElv', 'DE,AT'),
                (1, 1, 'allowedCurrencies', 'EUR,CHF'),
                (1, 1, 'company', '[Ihre Firmenbezeichnung]'),
                (1, 1, 'holder', 'Max Mustermann')
            ON DUPLICATE KEY UPDATE value = VALUES(`value`);
        ");
        $connection->query("
            INSERT INTO `bestit_payolution_config_order` (`name`, `order`) VALUES
                ('testmode', '1'),
                ('sender', '2'),
                ('login', '3'),
                ('passwd', '4'),
                ('channel_invoice', '5'),
                ('channel_installment', '6'),
                ('channel_b2b_invoice', '7'),
                ('channel_elv', '8'),
                ('channel_precheck', '9'),
                ('installment_payolution_user', '10'),
                ('installment_payolution_password', '11'),
                ('mail_bcc', '12'),
                ('bic', '13'),
                ('kontoinhaber', '14'),
                ('iban', '15'),
                ('logging', '16'),
                ('min_installment_value', '17'),
                ('max_installment_value', '18'),
                ('min_installment_detail_value', '19'),
                ('max_installment_detail_value', '20'),
                ('min_b2b_value', '21'),
                ('max_b2b_value', '22'),
                ('min_b2c_value', '23'),
                ('max_b2c_value', '24'),
                ('min_elv_value', '25'),
                ('max_elv_value', '26'),
                ('differentAddresses', '27'),
                ('allowedCountriesInvoiceB2C', '28'),
                ('allowedCountriesInvoiceB2B', '29'),
                ('allowedCountriesInstallment', '30'),
                ('allowedCountriesElv', '31'),
                ('allowedCurrencies', '32'),
                ('company', '33'),
                ('holder', '34')
            ON DUPLICATE KEY UPDATE `order` = VALUES(`order`)
        ");
    }

    /**
     * Create the payolution payments
     *
     * @return void
     *
     * @throws Exception
     */
    private function createPayments()
    {
        $activeFlags = [
            'payolution_invoice_b2c' => 0,
            'payolution_invoice_b2b' => 0,
            'payolution_installment' => 0,
            'payolution_elv' => 0,
        ];

        $payments = $this->modelManager->getRepository(Payment::class)->findAll();

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            if (array_key_exists($payment->getName(), $activeFlags)) {
                $activeFlags[$payment->getName()] = (int)$payment->getActive();
            }
        }

        $createdPayments = [];
        $payment = $this->paymentInstaller->createOrUpdate($this->pluginName, [
            'name' => 'payolution_invoice_b2c',
            'description' => 'Kauf auf Rechnung',
            'action' => 'PolPaymentPayolution',
            'active' => $activeFlags['payolution_invoice_b2c'],
            'position' => 1,
            'additionalDescription' => '<img src="{link file="frontend/_public/src/images/rechnung_icon_blue.png"}"/>',
        ]);

        $createdPayments['payolution_invoice_b2c'] = $payment->getId();

        $payment = $this->paymentInstaller->createOrUpdate($this->pluginName, [
            'name' => 'payolution_invoice_b2b',
            'description' => 'Firmenrechnung',
            'action' => 'PolPaymentPayolution',
            'active' => $activeFlags['payolution_invoice_b2b'],
            'position' => 2,
            'additionalDescription' => '<img src="{link file="frontend/_public/src/images/rechnung_icon_blue.png"}"/>',
        ]);

        $createdPayments['payolution_invoice_b2b'] = $payment->getId();

        $payment = $this->paymentInstaller->createOrUpdate($this->pluginName, [
            'name' => 'payolution_installment',
            'description' => 'Ratenkauf',
            'action' => 'PolPaymentPayolution',
            'active' => $activeFlags['payolution_installment'],
            'position' => 3,
            'additionalDescription' => '<img src="{link file="frontend/_public/src/images/icon_installment.png"}"/>',
        ]);

        $createdPayments['payolution_installment'] = $payment->getId();

        $payment = $this->paymentInstaller->createOrUpdate($this->pluginName, [
            'name' => 'payolution_elv',
            'description' => 'Lastschrift',
            'action' => 'PolPaymentPayolution',
            'active' => $activeFlags['payolution_elv'],
            'position' => 4,
            'additionalDescription' => '<img src="{link file="frontend/_public/src/images/elv_icon.png"}"/>',
        ]);

        $createdPayments['payolution_elv'] = $payment->getId();

        foreach (self::PAYMENT_TRANSLATIONS as $locale => $payments) {
            foreach ($payments as $payment => $description) {
                $this->updateTranslation($locale, 'config_payment', $createdPayments[$payment], [
                    'description' => $description
                ]);
            }
        }
    }

    /**
     * Add attributes for the plugin
     *
     * @return void
     *
     * @throws Exception
     */
    private function addAttributes()
    {
        $this->crudService->update(
            's_order_attributes',
            'payolution_capture',
            TypeMapping::TYPE_FLOAT,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_rv',
            TypeMapping::TYPE_INTEGER,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_cr_type',
            TypeMapping::TYPE_INTEGER,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_refund',
            TypeMapping::TYPE_FLOAT,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_details_attributes',
            'payolution_capture',
            TypeMapping::TYPE_FLOAT,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_details_attributes',
            'payolution_refund',
            TypeMapping::TYPE_FLOAT,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_unique_id',
            TypeMapping::TYPE_SINGLE_SELECTION
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_payment_reference_id',
            TypeMapping::TYPE_SINGLE_SELECTION
        );

        $this->crudService->update(
            's_user_attributes',
            'payolution_payment_reference_id_temp',
            TypeMapping::TYPE_SINGLE_SELECTION
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_difference',
            TypeMapping::TYPE_INTEGER,
            [],
            null,
            false,
            0
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_invoice_id',
            TypeMapping::TYPE_SINGLE_SELECTION,
            [],
            null,
            false,
            '0'
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_shipping',
            TypeMapping::TYPE_FLOAT,
            [
                'label' => '',
                'supportText' => '',
                'helpText' => '',
                'translatable' => false,
                'displayInBackend' => false,
                'position' => 100,
                'custom' => true,
            ]
        );

        $this->crudService->update(
            's_order_attributes',
            'payolution_session_id',
            TypeMapping::TYPE_SINGLE_SELECTION,
            [
                'label' => '',
                'supportText' => '',
                'helpText' => '',
                'translatable' => false,
                'displayInBackend' => false,
                'position' => 100,
                'custom' => true,
            ]
        );

        $metaDataCache = $this->modelManager->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        $this->modelManager->generateAttributeModels(
            [
                's_order_attributes',
                's_order_details_attributes',
                's_user_attributes'
            ]
        );
    }

    /**
     * Create or drop schema for models.
     *
     * @param bool $create Create or drop?
     *
     * @return bool
     *
     * @throws DBALException
     */
    private function modifySchema()
    {
        $connection = $this->modelManager->getConnection();
        $connection->query("
            CREATE TABLE IF NOT EXISTS `payolution_workflow_element` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `additional_identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `amount` double NOT NULL,
              `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `quantity` int(11) NOT NULL,
              `created` date NOT NULL,
              `last_modified` date NOT NULL,
              `captured` tinyint(1) NOT NULL,
              `refunded` tinyint(1) NOT NULL,
              `captured_quantity` int(11) NOT NULL,
              `refunded_quantity` int(11) NOT NULL,
              `order_id` int(11) NOT NULL,
              `order_detail_id` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $connection->query(
            "
            CREATE TABLE IF NOT EXISTS `payolution_workflow_history` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `amount` double NOT NULL,
              `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `quantity` int(11) NOT NULL,
              `capture_date` date NOT NULL,
              `order_id` int(11) NOT NULL,
              `success` tinyint(1) NOT NULL,
              `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `request` longtext COLLATE utf8_unicode_ci,
              `response` longtext COLLATE utf8_unicode_ci,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    /**
     * Get the id of an shopware document
     *
     * @param string $docName The name of the sw document
     *
     * @return int
     */
    private function getIdOfDocument($docName)
    {
        $sql = <<<sql
SELECT id
FROM `s_core_documents_box`
WHERE `name` = :docName;
sql;

        $id = $this->modelManager->getConnection()->fetchColumn($sql, ['docName' => $docName,]);

        return $id ? (int)$id : null;
    }

    /**
     * Update an shopware document by given id and params
     *
     * @param int $id The id of the document
     * @param string $content The content of the document
     * @param string $style The custom styling of the content
     *
     * @return void
     *
     * @throws DBALException
     */
    private function updateShopwareDocument($id, $content, $style)
    {
        $sql = <<<SQL
UPDATE
  `s_core_documents_box`
SET `value` = :value,
    `style` = :style
WHERE id = :id
SQL;
        $this->modelManager->getConnection()->executeQuery($sql, [
            ':style' => $style,
            ':value' => $content,
            ':id' => $id
        ]);
    }

    /**
     * Create an shopware document by given params
     *
     * @param string $content The content of the document
     * @param string $style The custom styling of the content
     * @param string $name The name of the template
     *
     * @return void
     *
     * @throws DBALException
     */
    private function createShopwareDocument($content, $style, $name)
    {
        $sql = <<<SQL
INSERT INTO `s_core_documents_box`
  (`documentID`, `name`, `style`, `value`)
VALUES (1, :name, :style, :value);
SQL;
        $this->modelManager->getConnection()->executeQuery($sql, [
            ':name' => $name,
            ':style' => $style,
            ':value' => $content
        ]);
    }

    /**
     * Update the translations
     *
     * @param string $locale
     * @param string $type
     * @param int $key
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    private function updateTranslation($locale, $type, $key, array $data)
    {
        $locale = $this->modelManager->getRepository(Locale::class)->findOneBy(['locale' => $locale]);

        $sql = <<<sql
SELECT id FROM s_core_shops scs WHERE scs.locale_id = :locale
sql;
        $connection = $this->modelManager->getConnection();
        $result = $connection->fetchAll($sql, ['locale' => $locale->getId()]);

        // Shopware translations are mapped over the shop id so we need to add the translation to all shops with the
        // locale
        foreach (array_column($result, 'id') as $shopId) {
            $this->translator->write(
                $shopId,
                $type,
                $key,
                $data,
                true
            );
        }
    }
}
