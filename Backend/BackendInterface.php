<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

interface BackendInterface
{
    /**
     * Returns the configured sections
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSections();

    /**
     * Loads the item by its ID
     *
     * @param int|string $itemId
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadItem($itemId);

    /**
     * Returns the item children.
     *
     * @param int|string $itemId
     * @param array $params
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getChildren($itemId, array $params = array());

    /**
     * Returns the item children count.
     *
     * @param int|string $itemId
     * @param array $params
     *
     * @return int
     */
    public function getChildrenCount($itemId, array $params = array());

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param array $params
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function search($searchText, array $params = array());
}
