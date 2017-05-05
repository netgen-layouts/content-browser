<?php

namespace Netgen\ContentBrowser\Config;

interface ConfigLoaderInterface
{
    /**
     * Loads the configuration for provided item type.
     *
     * @param string $itemType
     * @param string $configName
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config could not be found
     *
     * @return \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    public function loadConfig($itemType, $configName);
}
