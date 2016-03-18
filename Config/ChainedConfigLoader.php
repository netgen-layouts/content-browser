<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

class ChainedConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface
     */
    protected $defaultConfigLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface $defaultConfigLoader
     */
    public function __construct(ConfigLoaderInterface $defaultConfigLoader)
    {
        $this->defaultConfigLoader = $defaultConfigLoader;
    }

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface[]
     */
    protected $configLoaders = array();

    /**
     * Adds a config loader to chained config
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface $configLoader
     */
    public function addConfigLoader(ConfigLoaderInterface $configLoader)
    {
        $this->configLoaders[] = $configLoader;
    }

    /**
     * Loads the configuration by its name
     *
     * @param string $configName
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If config could not be found
     *
     * @return array
     */
    public function loadConfig($configName)
    {
        $loadedConfig = array();

        foreach ($this->configLoaders as $configLoader) {
            if (!$configLoader->supports($configName)) {
                continue;
            }

            $loadedConfig = $configLoader->loadConfig($configName);
            break;
        }

        if (!empty($loadedConfig)) {
            $itemType = $loadedConfig['item_type'];
            return $loadedConfig + $this->defaultConfigLoader->loadConfig(
                $itemType
            );
        }

        return $this->defaultConfigLoader->loadConfig($configName);
    }

    /**
     * Returns if the loader supports the config with provided name.
     *
     * @param string $configName
     *
     * @return bool
     */
    public function supports($configName)
    {
        return true;
    }
}
