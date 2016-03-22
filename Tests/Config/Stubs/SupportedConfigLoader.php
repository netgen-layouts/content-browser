<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Config\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Config\NamedConfigLoaderInterface;

class SupportedConfigLoader implements NamedConfigLoaderInterface
{
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
        return array('one' => 'supported', 'two' => 'supported');
    }

    /**
     * Returns the item type which this config supports.
     *
     * @return string
     */
    public function getItemType()
    {
        return 'ezcontent';
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
