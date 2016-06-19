<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ItemSerializerInterface
{
    /**
     * Serializes the item to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item);

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[] $items
     *
     * @return array
     */
    public function serializeItems(array $items);
}
