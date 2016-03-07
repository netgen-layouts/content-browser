<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tree;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException;

class Tree implements TreeInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface $adapter
     * @param array $config
     */
    public function __construct(
        AdapterInterface $adapter,
        array $config
    ) {
        $this->adapter = $adapter;
        $this->config = $config;
    }

    /**
     * Returns the configured adapter.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Returns the tree config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns all root items.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item[]
     */
    public function getRootItems()
    {
        $rootItems = array();

        foreach ($this->config['root_locations'] as $rootItem) {
            $rootItems[] = $this->getItem($rootItem);
        }

        return $rootItems;
    }

    /**
     * Loads the item for specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException If item is outside of root items
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item
     */
    public function getItem($itemId)
    {
        $item = $this->adapter->loadItem($itemId);

        if (!$this->isInsideRootItems($item)) {
            throw new OutOfBoundsException("Item #{$itemId} is not inside root items.");
        }

        return $item;
    }

    /**
     * Loads all children of the specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item[]
     */
    public function getChildren(Item $item)
    {
        return $this->adapter->loadItemChildren(
            $item,
            $this->getChildrenTypes()
        );
    }

    /**
     * Returns if current item has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function hasChildren(Item $item)
    {
        return $this->adapter->hasChildren(
            $item,
            $this->getChildrenTypes()
        );
    }

    /**
     * Loads all categories below the specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Item[]
     */
    public function getSubCategories(Item $item)
    {
        return $this->adapter->loadItemChildren(
            $item,
            $this->getCategoryTypes()
        );
    }

    /**
     * Returns if current item has child categories.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function hasSubCategories(Item $item)
    {
        return $this->adapter->hasChildren(
            $item,
            $this->getCategoryTypes()
        );
    }

    /**
     * Returns if provided item is one of the root items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function isRootItem(Item $item)
    {
        foreach ($this->config['root_locations'] as $rootItem) {
            if ($item->id == $rootItem) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if provided item is inside one of the root items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Item $item
     *
     * @return bool
     */
    public function isInsideRootItems(Item $item)
    {
        foreach ($this->config['root_locations'] as $rootItem) {
            if (in_array($rootItem, $item->path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns types used to filter the children list
     *
     * @return array
     */
    protected function getChildrenTypes()
    {
        $types = array();
        if (!empty($this->config['children']['types'])) {
            $types = $this->config['children']['types'];

            if (!empty($this->config['children']['include_category_types'])) {
                $types = array_merge($types, $this->getCategoryTypes());
            }
        }

        return $types;
    }

    /**
     * Returns types used to filter the categories list
     *
     * @return array
     */
    protected function getCategoryTypes()
    {
        if (!empty($this->config['categories']['types']))
        {
            return $this->config['categories']['types'];
        }

        return array();
    }
}
