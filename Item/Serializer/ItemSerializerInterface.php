<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ItemSerializerInterface
{
    /**
     * Serializes the item to array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item);

    /**
     * Serializes the location to array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return array
     */
    public function serializeLocation(LocationInterface $location);

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[] $items
     *
     * @return array
     */
    public function serializeItems(array $items);

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[] $locations
     *
     * @return array
     */
    public function serializeLocations(array $locations);
}
