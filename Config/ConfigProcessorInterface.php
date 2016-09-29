<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

interface ConfigProcessorInterface
{
    /**
     * Returns the item type which this config processor supports.
     *
     * @return string
     */
    public function getItemType();

    /**
     * Returns if the processor supports the config with provided name.
     *
     * @param string $configName
     *
     * @return bool
     */
    public function supports($configName);

    /**
     * Processes the given config.
     *
     * @param string $configName
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface $config
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If config could not be found
     */
    public function processConfig($configName, $config);
}
