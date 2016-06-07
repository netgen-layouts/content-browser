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
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\NamedConfigLoaderInterface[] $configLoaders
     */
    public function __construct(ConfigLoaderInterface $defaultConfigLoader, array $configLoaders = array())
    {
        $this->defaultConfigLoader = $defaultConfigLoader;
        $this->configLoaders = $configLoaders;
    }

    /**
     * Loads the configuration by its name.
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
