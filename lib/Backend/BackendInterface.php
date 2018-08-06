<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Item\LocationInterface;

interface BackendInterface
{
    /**
     * Returns the default sections available in the backend.
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface[]
     */
    public function getSections();

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface
     */
    public function loadLocation($id);

    /**
     * Loads the item by its value.
     *
     * @param int|string $value
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    public function loadItem($value);

    /**
     * Returns the locations below provided location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location);

    /**
     * Returns the count of locations below provided location.
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
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface[]
     */
    public function search($searchText, $offset = 0, $limit = 25);

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     *
     * @return int
     */
    public function searchCount($searchText);
}
