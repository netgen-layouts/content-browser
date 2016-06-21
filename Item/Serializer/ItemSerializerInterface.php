<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
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
     * Serializes the category to array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return array
     */
    public function serializeCategory(CategoryInterface $category);

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
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[] $categories
     *
     * @return array
     */
    public function serializeCategories(array $categories);
}
