<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ItemRepositoryInterface
{
    /**
     * Returns the default sections available in the backend.
     *
     * @param string $valueType
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getDefaultSections($valueType);

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface
     */
    public function loadLocation($id, $valueType);

    /**
     * Loads the item by its value ID.
     *
     * @param int|string $id
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadItem($id, $valueType);

    /**
     * Returns the location children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location);

    /**
     * Returns the location children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubLocationsCount(LocationInterface $location);

    /**
     * Returns the location items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25);

    /**
     * Returns the location items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubItemsCount(LocationInterface $location);

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param string $valueType
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function search($searchText, $valueType, $offset = 0, $limit = 25);

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     * @param string $valueType
     *
     * @return int
     */
    public function searchCount($searchText, $valueType);
}
