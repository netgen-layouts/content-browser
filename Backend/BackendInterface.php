<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

interface BackendInterface
{
    /**
     * Returns the configured sections.
     *
     * @return array
     */
    public function getSections();

    /**
     * Loads the item by its ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return mixed
     */
    public function loadItem($itemId);

    /**
     * Returns the item children.
     *
     * @param int|string $itemId
     * @param array $params
     *
     * @return array
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
     * @return array
     */
    public function search($searchText, array $params = array());

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     * @param array $params
     *
     * @return int
     */
    public function searchCount($searchText, array $params = array());
}
