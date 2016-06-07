<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

use JsonSerializable;

interface ItemInterface extends JsonSerializable
{
    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();

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
     * Returns the item template variables.
     *
     * @return array
     */
    public function getTemplateVariables();

    /**
     * Returns the item columns.
     *
     * @return array
     */
    public function getColumns();
}
