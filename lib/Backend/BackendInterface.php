<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Backend;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

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
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If location does not exist
     */
    public function loadLocation(int|string $id): LocationInterface;

    /**
     * Loads the item by its value.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\NotFoundException If item does not exist
     */
    public function loadItem(int|string $value): ItemInterface;

    /**
     * Returns the locations below provided location.
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
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable;

    /**
     * Returns the location items count.
     */
    public function getSubItemsCount(LocationInterface $location): int;

    /*
     * Searches for items.
     */
    public function searchItems(SearchQuery $searchQuery): SearchResultInterface;

    /*
     * Returns the count of searched items.
     */
    public function searchItemsCount(SearchQuery $searchQuery): int;
}
