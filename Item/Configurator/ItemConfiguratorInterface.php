<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Configurator;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ItemConfiguratorInterface
{
    /**
     * Configures the item based on current config.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItemInterface
     */
    public function configureItem(ItemInterface $item);
}
