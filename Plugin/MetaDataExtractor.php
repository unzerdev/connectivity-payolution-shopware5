<?php

namespace PolPaymentPayolution\Plugin;

use Shopware\Components\Plugin\XmlPluginInfoReader;

/**
 * Extractor to get the plugin meta data
 *
 * @package PolPaymentPayolution\Plugin
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class MetaDataExtractor
{
    /**
     * The filename for the meta data file
     *
     * @var string
     */
    const META_DATA_FILE_NAME = 'plugin.xml';

    /**
     * The path where the metadata file is stored
     *
     * @var string
     */
    private $metaDataPath;

    /**
     * Cache to only parse the meta data once
     *
     * @var array|null
     */
    private $metaDataCache;

    /**
     * MetaDataExtractor constructor.
     *
     * @param string $metaDataPath
     */
    public function __construct($metaDataPath)
    {
        $this->metaDataPath = $metaDataPath;
    }

    /**
     * Get the plugin version
     *
     * @return string
     */
    public function getPluginVersion()
    {
        $metaData = $this->getMetaData();

        // Set plugin version 0.0.1 as fallback if no version is given
        // @see \Shopware\Bundle\PluginInstallerBundle\Service\PluginInstaller::refreshPluginList
        return isset($metaData['version']) ? $metaData['version'] : '0.0.1';
    }

    /**
     * Get meta data from the configured file
     *
     * @return array
     */
    private function getMetaData()
    {
        if ($this->metaDataCache === null) {
            $xmlConfigReader = new XmlPluginInfoReader();
            $this->metaDataCache = $xmlConfigReader->read(
                $this->metaDataPath . DIRECTORY_SEPARATOR . self::META_DATA_FILE_NAME
            );
        }

        return $this->metaDataCache;
    }
}
