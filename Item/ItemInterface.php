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
     * Sets the item ID.
     *
     * @param int|string $id
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setId($id);

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Sets the item value.
     *
     * @param int|string $value
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setValue($value);

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Sets the item parent ID.
     *
     * @param int|string $parentId
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setParentId($parentId);

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the item name.
     *
     * @param string $name
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setName($name);

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable();

    /**
     * Sets if the item is selectable.
     *
     * @param bool $isSelectable
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setIsSelectable($isSelectable);

    /**
     * Returns if the item has children.
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Sets if the item has children.
     *
     * @param bool $hasChildren
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setHasChildren($hasChildren);

    /**
     * Returns if the item has subcategories.
     *
     * @return bool
     */
    public function hasSubCategories();

    /**
     * Sets if the item has subcategories.
     *
     * @param bool $hasSubCategories
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setHasSubCategories($hasSubCategories);

    /**
     * Returns the item template variables.
     *
     * @return array
     */
    public function getTemplateVariables();

    /**
     * Sets the item template variables.
     *
     * @param array $templateVariables
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setTemplateVariables(array $templateVariables);

    /**
     * Returns the item columns.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Sets the item columns.
     *
     * @param array $columns
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setColumns(array $columns);
}
