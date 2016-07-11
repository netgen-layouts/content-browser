<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerHandlerInterface;

class ItemSerializerHandler implements ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item)
    {
        return true;
    }
}
