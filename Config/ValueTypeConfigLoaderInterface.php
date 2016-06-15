<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

interface ValueTypeConfigLoaderInterface extends ConfigLoaderInterface
{
    /**
     * Returns the value type which this config supports.
     *
     * @return string
     */
    public function getValueType();

    /**
     * Returns if the loader supports the config with provided name.
     *
     * @param string $configName
     *
     * @return bool
     */
    public function supports($configName);
}
