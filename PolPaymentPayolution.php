<?php

namespace PolPaymentPayolution;

use Doctrine\DBAL\DBALException;
use Exception;
use Payolution\DependecyInjection\ShopwareCompatibilityPass;
use Payolution\DependecyInjection\SaveHandlerPass;
use PolPaymentPayolution\Plugin\MetaDataExtractor;
use Shopware;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware_Components_Translation;
use PolPaymentPayolution\Setup\Installer;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Payolution Plugin base class
 *
 * @package PolPaymentPayolution
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PolPaymentPayolution extends Plugin
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * Installs the plugin
     *
     * @param InstallContext $context The context for the install process
     *
     * @return void
     *
     * @throws Exception
     */
    public function install(InstallContext $context)
    {
        $this->getInstaller()->syncPlugin();

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
        parent::install($context);
    }

    /**
     * Updates the plugin
     *
     * @param $context $context The context for the update process
     *
     * @return void
     *
     * @throws Exception
     */
    public function update(UpdateContext $context)
    {
        $installer = $this->getInstaller();
        $installer->syncPlugin();
        $installer->syncPluginConfig($context->getCurrentVersion());

        $context->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
        parent::update($context);
    }

    /**
     * Remove widget and remove database schema.
     *
     * @param UninstallContext $context The context for the uninstall process
     *
     * @throws Exception
     */
    public function uninstall(UninstallContext $context)
    {
        $this->getInstaller()->deactivatePayments();
        $context->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);

        parent::uninstall($context);
    }

    /**
     * Deactivate the plugin
     *
     * @param DeactivateContext $context The context
     *
     * @return void
     *
     * @throws DBALException
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->getInstaller()->deactivatePayments();

        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);

        parent::deactivate($context);
    }

    /**
     * Builds the Plugin.
     *
     * @inheritdoc
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        $activePlugins = $container->hasParameter('active_plugins')
            ? $container->getParameter('active_plugins')
            : [];
        $pickwareActive = in_array('ViisonPickwareERP', array_keys($activePlugins));

        $container->setParameter($this->getContainerPrefix() . '.pickware_active', $pickwareActive);

        $container->setParameter(
            $this->getContainerPrefix() . '.plugin_version',
            (new MetaDataExtractor($this->getPath()))->getPluginVersion()
        );

        $container->addCompilerPass(new SaveHandlerPass());

        parent::build($container);
    }

    /**
     * Get the plugin installer
     *
     * @return Installer
     */
    public function getInstaller()
    {
        if ($this->installer === null) {
            $version = $this->container->hasParameter('shopware.release.version')
                ? $this->container->getParameter('shopware.release.version')
                : Shopware::VERSION;

            $this->installer = new Installer(
                $this->container->get('shopware.plugin_payment_installer'),
                $this->container->get('models'),
                $this->container->get('shopware_attribute.crud_service'),
                $this->getTranslationComponent($version),
                $this->getName(),
                $this->getPath()
            );
        }

        return $this->installer;
    }

    /**
     * Get the shopware translation component
     *
     * @param string $version The shopware version
     *
     * @return Shopware_Components_Translation
     */
    private function getTranslationComponent($version)
    {
        $connection = $this->container->get('models')->getConnection();

        if (version_compare($version, '5.6', '>=')) {
            $component = new Shopware_Components_Translation(
                $connection,
                $this->container
            );
        } else {
            $component = new Shopware_Components_Translation(
                $connection
            );
        }

        return $component;
    }
}
