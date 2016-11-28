<?php

namespace Netgen\ContentBrowser\Item;

interface ItemRepositoryInterface
{
    /**
     * Returns the default sections available in the backend.
     *
     * @param string $itemType
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface[]
     */
    public function getDefaultSections($itemType);

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     * @param string $itemType
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface
     */
    public function loadLocation($id, $itemType);

    /**
     * Loads the item by its ID.
     *
     * @param int|string $id
     * @param string $itemType
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    public function loadItem($id, $itemType);

    /**
     * Returns the location children.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location);

    /**
     * Returns the location children count.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubLocationsCount(LocationInterface $location);

    /**
     * Returns the location items.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface[]
     */
    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25);

    /**
     * Returns the location items count.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubItemsCount(LocationInterface $location);

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param string $itemType
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface[]
     */
    public function search($searchText, $itemType, $offset = 0, $limit = 25);

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     * @param string $itemType
     *
     * @return int
     */
    public function searchCount($searchText, $itemType);
}
