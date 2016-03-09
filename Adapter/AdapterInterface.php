<?php

namespace Netgen\Bundle\ContentBrowserBundle\Adapter;

use Netgen\Bundle\ContentBrowserBundle\Item\Item;

interface AdapterInterface
{
    /**
     * Returns all available columns and their names
     *
     * @return array
     */
    public function getColumns();

    /**
     * Loads the item for provided ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item with provided ID was not found
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Item
     */
    public function loadItem($itemId);

    /**
     * Loads all children of the provided item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Item $item
     * @param string[] $types
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Item[]
     */
    public function loadItemChildren(Item $item, array $types = array());

    /**
     * Returns true if provided item has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Item $item
     * @param string[] $types
     *
     * @return bool
     */
    public function hasChildren(Item $item, array $types = array());

    /**
     * Returns items found with search text
     *
     * @param string $searchText
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\Item[]
     */
    public function search($searchText);
}
