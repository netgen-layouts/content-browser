<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

interface ItemSerializerInterface
{
    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[] $items
     *
     * @return array
     */
    public function serialize(array $items);
}
