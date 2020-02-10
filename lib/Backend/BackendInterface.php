<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

/**
 * @method \Netgen\ContentBrowser\Backend\SearchResultInterface searchItems(SearchQuery $searchQuery)
 * @method int searchItemsCount(SearchQuery $searchQuery)
 */
interface BackendInterface
{
    /**
     * Returns the sections available in the backend.
     *
     * @return iterable<\Netgen\ContentBrowser\Item\LocationInterface>
     */
    public function getSections(): iterable;

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\ContentBrowser\Item\LocationInterface
     */
    public function loadLocation($id): LocationInterface;

    /**
     * Loads the item by its value.
     *
     * @param int|string $value
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    public function loadItem($value): ItemInterface;

    /**
     * Returns the locations below provided location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return iterable<\Netgen\ContentBrowser\Item\LocationInterface>
     */
    public function getSubLocations(LocationInterface $location): iterable;

    /**
     * Returns the count of locations below provided location.
     */
    public function getSubLocationsCount(LocationInterface $location): int;

    /**
     * Returns the location items.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable;

    /**
     * Returns the location items count.
     */
    public function getSubItemsCount(LocationInterface $location): int;

    /**
     * Searches for items.
     *
     * @deprecated in favor BackendInterface::searchItems. Will be removed in 2.0.
     *
     * @param string $searchText
     * @param int $offset
     * @param int $limit
     *
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function search(string $searchText, int $offset = 0, int $limit = 25): iterable;

    /**
     * Returns the count of searched items.
     *
     * @deprecated in favor BackendInterface::searchItemsCount. Will be removed in 2.0.
     */
    public function searchCount(string $searchText): int;

    /*
     * Searches for items.
     *
     * Will be added to interface in 2.0.
     */
    // public function searchItems(SearchQuery $searchQuery): SearchResultInterface;

    /*
     * Returns the count of searched items.
     *
     * Will be added to interface in 2.0.
     */
    // public function searchItemsCount(SearchQuery $searchQuery): int;
}
