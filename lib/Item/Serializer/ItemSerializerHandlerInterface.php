<?php

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item);
}
