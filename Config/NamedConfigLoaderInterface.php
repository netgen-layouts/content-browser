<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

interface NamedConfigLoaderInterface extends ConfigLoaderInterface
{
    /**
     * Returns the item type which this config supports.
     *
     * @return string
     */
    public function getItemType();

    /**
     * Returns if the loader supports the config with provided name.
     *
     * @param string $configName
     *
     * @return bool
     */
    public function supports($configName);
}
