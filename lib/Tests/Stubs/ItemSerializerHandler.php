<?php

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerHandlerInterface;

class ItemSerializerHandler implements ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item)
    {
        return true;
    }
}
