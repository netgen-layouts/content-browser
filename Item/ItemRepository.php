<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

use Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface;

class ItemRepository implements ItemRepositoryInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface $backendRegistry
     */
    public function __construct(BackendRegistryInterface $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
    }

    /**
     * Returns the default sections available in the backend.
     *
     * @param string $itemType
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getDefaultSections($itemType)
    {
        return $this->backendRegistry->getBackend($itemType)->getDefaultSections();
    }

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     * @param string $itemType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface
     */
    public function loadLocation($id, $itemType)
    {
        return $this->backendRegistry->getBackend($itemType)->loadLocation($id);
    }

    /**
     * Loads the item by its ID.
     *
     * @param int|string $id
     * @param string $itemType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadItem($id, $itemType)
    {
        return $this->backendRegistry->getBackend($itemType)->loadItem($id);
    }

    /**
     * Returns the location children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location)
    {
        $backend = $this->backendRegistry->getBackend($location->getType());

        return $backend->getSubLocations($location);
    }

    /**
     * Returns the location children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubLocationsCount(LocationInterface $location)
    {
        $backend = $this->backendRegistry->getBackend($location->getType());

        return $backend->getSubLocationsCount($location);
    }

    /**
     * Returns the location items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
        $backend = $this->backendRegistry->getBackend($location->getType());

        return $backend->getSubItems($location, $offset, $limit);
    }

    /**
     * Returns the location items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubItemsCount(LocationInterface $location)
    {
        $backend = $this->backendRegistry->getBackend($location->getType());

        return $backend->getSubItemsCount($location);
    }

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param string $itemType
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function search($searchText, $itemType, $offset = 0, $limit = 25)
    {
        $backend = $this->backendRegistry->getBackend($itemType);

        return $backend->search($searchText, $offset, $limit);
    }

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     * @param string $itemType
     *
     * @return int
     */
    public function searchCount($searchText, $itemType)
    {
        $backend = $this->backendRegistry->getBackend($itemType);

        return $backend->searchCount($searchText);
    }
}
