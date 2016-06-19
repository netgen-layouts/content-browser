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
     * Loads the item by its ID.
     *
     * @param int|string $id
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function load($id, $valueType)
    {
        return $this->backendRegistry->getBackend($valueType)->load($id);
    }

    /**
     * Loads the item by its value ID.
     *
     * @param int|string $id
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If value does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadByValue($id, $valueType)
    {
        return $this->backendRegistry->getBackend($valueType)->loadByValue($id);
    }

    /**
     * Returns the category children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubCategories(ItemInterface $item)
    {
        $backend = $this->backendRegistry->getBackend($item->getValueType());

        return $backend->getSubCategories($item);
    }

    /**
     * Returns the category children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return int
     */
    public function getSubCategoriesCount(ItemInterface $item)
    {
        $backend = $this->backendRegistry->getBackend($item->getValueType());

        return $backend->getSubCategoriesCount($item);
    }

    /**
     * Returns the item children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getChildren(ItemInterface $item, $offset = 0, $limit = 25)
    {
        $backend = $this->backendRegistry->getBackend($item->getValueType());

        return $backend->getChildren($item, $offset, $limit);
    }

    /**
     * Returns the item children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return int
     */
    public function getChildrenCount(ItemInterface $item)
    {
        $backend = $this->backendRegistry->getBackend($item->getValueType());

        return $backend->getChildrenCount($item);
    }

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
    public function search($searchText, $valueType, $offset = 0, $limit = 25)
    {
        $backend = $this->backendRegistry->getBackend($valueType);

        return $backend->search($searchText, $offset, $limit);
    }

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     * @param string $valueType
     *
     * @return int
     */
    public function searchCount($searchText, $valueType)
    {
        $backend = $this->backendRegistry->getBackend($valueType);

        return $backend->searchCount($searchText);
    }
}
