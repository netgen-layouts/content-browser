<?php

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

interface ItemSerializerInterface
{
    /**
     * Serializes the item to array.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item);

    /**
     * Serializes the location to array.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return array
     */
    public function serializeLocation(LocationInterface $location);

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface[] $items
     *
     * @return array
     */
    public function serializeItems(array $items);

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface[] $locations
     *
     * @return array
     */
    public function serializeLocations(array $locations);
}
