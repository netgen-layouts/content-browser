<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ItemInterface
{
    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the value type.
     *
     * @return int|string
     */
    public function getValueType();

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable();

    /**
     * Returns if the item has children.
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Returns if the item has subcategories.
     *
     * @return bool
     */
    public function hasSubCategories();

    /**
     * Returns the object.
     *
     * @return bool
     */
    public function getObject();
}
