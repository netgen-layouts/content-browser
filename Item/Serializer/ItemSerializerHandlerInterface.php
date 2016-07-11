<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item);
}
