<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\ConfiguratorHandlerInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class ConfiguratorHandler implements ConfiguratorHandlerInterface
{
    /**
     * Returns if the item is selectable based on provided config.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param array $config
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item, array $config)
    {
        return true;
    }
}
