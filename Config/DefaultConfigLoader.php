<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DefaultConfigLoader implements ConfigLoaderInterface
{
    use ContainerAwareTrait;

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
        $param = 'netgen_content_browser.config.' . $configName;

        if (!$this->container->hasParameter($param)) {
            throw new InvalidArgumentException(
                "Configuration for '{$configName}' item does not exist."
            );
        }

        $config = $this->container->getParameter($param);
        $config['value_type'] = $configName;

        return $config;
    }
}
