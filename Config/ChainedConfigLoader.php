<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

class ChainedConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface
     */
    protected $defaultConfigLoader;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\NamedConfigLoaderInterface[]
     */
    protected $configLoaders = array();

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
     * Adds a config loader to chained config
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\NamedConfigLoaderInterface $configLoader
     */
    public function addConfigLoader(NamedConfigLoaderInterface $configLoader)
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
        foreach ($this->configLoaders as $configLoader) {
            if (!$configLoader->supports($configName)) {
                continue;
            }

            $defaultConfig = $this->defaultConfigLoader->loadConfig(
                $configLoader->getItemType()
            );

            $loadedConfig = $configLoader->loadConfig($configName);
            return $loadedConfig + $defaultConfig;
        }

        return $this->defaultConfigLoader->loadConfig($configName);
    }
}
