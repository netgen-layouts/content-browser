<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;

interface BackendInterface
{
    /**
     * Returns the default sections available in the backend.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    public function getDefaultSections();

    /**
     * Loads a  category by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If category does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface
     */
    public function loadCategory($id);

    /**
     * Loads the item by its value ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadItem($id);

    /**
     * Returns the categories below provided category.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[]
     */
    public function getSubCategories(CategoryInterface $category);

    /**
     * Returns the count of categories below provided category.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return int
     */
    public function getSubCategoriesCount(CategoryInterface $category);

    /**
     * Returns the category items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(CategoryInterface $category, $offset = 0, $limit = 25);

    /**
     * Returns the category items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return int
     */
    public function getSubItemsCount(CategoryInterface $category);

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
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
