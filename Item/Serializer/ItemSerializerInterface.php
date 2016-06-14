<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface;

interface ItemSerializerInterface
{
    /**
     * Serializes the item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item);

    /**
     * Serializes the item reference.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface $itemReference
     *
     * @return array
     */
    public function serializeItemReference(ItemReferenceInterface $itemReference);

    /**
     * Builds items from specified values and serializes them to an array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[] $values
     *
     * @return array
     */
    public function serializeValues(array $values);
}
