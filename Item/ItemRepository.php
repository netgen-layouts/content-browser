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
     * @param string $valueType
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    public function getDefaultSections($valueType)
    {
        return $this->backendRegistry->getBackend($valueType)->getDefaultSections();
    }

    /**
     * Loads a  category by its ID.
     *
     * @param int|string $id
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If category does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface
     */
    public function loadCategory($id, $valueType)
    {
        return $this->backendRegistry->getBackend($valueType)->loadCategory($id);
    }

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
    public function loadItem($id, $valueType)
    {
        return $this->backendRegistry->getBackend($valueType)->loadItem($id);
    }

    /**
     * Returns the category children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    public function getSubCategories(CategoryInterface $category)
    {
        $backend = $this->backendRegistry->getBackend($category->getType());

        return $backend->getSubCategories($category);
    }

    /**
     * Returns the category children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return int
     */
    public function getSubCategoriesCount(CategoryInterface $category)
    {
        $backend = $this->backendRegistry->getBackend($category->getType());

        return $backend->getSubCategoriesCount($category);
    }

    /**
     * Returns the category items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(CategoryInterface $category, $offset = 0, $limit = 25)
    {
        $backend = $this->backendRegistry->getBackend($category->getType());

        return $backend->getSubItems($category, $offset, $limit);
    }

    /**
     * Returns the category items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return int
     */
    public function getSubItemsCount(CategoryInterface $category)
    {
        $backend = $this->backendRegistry->getBackend($category->getType());

        return $backend->getSubItemsCount($category);
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
