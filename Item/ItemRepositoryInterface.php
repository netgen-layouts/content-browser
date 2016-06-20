<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ItemRepositoryInterface
{
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
    public function load($id, $valueType);

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
    public function loadByValue($id, $valueType);

    /**
     * Returns the category children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubCategories(ItemInterface $item);

    /**
     * Returns the category children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return int
     */
    public function getSubCategoriesCount(ItemInterface $item);

    /**
     * Returns the item children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(ItemInterface $item, $offset = 0, $limit = 25);

    /**
     * Returns the item children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return int
     */
    public function getSubItemsCount(ItemInterface $item);

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
