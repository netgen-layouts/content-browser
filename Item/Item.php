<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class Item implements ItemInterface
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $value;

    /**
     * @var int|string
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isSelectable;

    /**
     * @var bool
     */
    protected $hasChildren;

    /**
     * @var bool
     */
    protected $hasSubCategories;

    /**
     * @var array
     */
    protected $templateVariables = array();

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the item ID.
     *
     * @param int|string $id
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the item value.
     *
     * @param int|string $value
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Sets the item parent ID.
     *
     * @param int|string $parentId
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the item name.
     *
     * @param string $name
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return $this->isSelectable;
    }

    /**
     * Sets if the item is selectable.
     *
     * @param bool $isSelectable
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setIsSelectable($isSelectable)
    {
        $this->isSelectable = $isSelectable;

        return $this;
    }

    /**
     * Returns if the item has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * Sets if the item has children.
     *
     * @param bool $hasChildren
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setHasChildren($hasChildren)
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    /**
     * Returns if the item has subcategories.
     *
     * @return bool
     */
    public function hasSubCategories()
    {
        return $this->hasSubCategories;
    }

    /**
     * Sets if the item has subcategories.
     *
     * @param bool $hasSubCategories
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setHasSubCategories($hasSubCategories)
    {
        $this->hasSubCategories = $hasSubCategories;

        return $this;
    }

    /**
     * Returns the item template variables.
     *
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables;
    }

    /**
     * Sets the item template variables.
     *
     * @param array $templateVariables
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setTemplateVariables(array $templateVariables)
    {
        $this->templateVariables = $templateVariables;

        return $this;
    }

    /**
     * Returns the item columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Sets the item columns.
     *
     * @param array $columns
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Specifies data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'parent_id' => $this->getParentId(),
            'name' => $this->getName(),
            'selectable' => $this->isSelectable(),
            'has_children' => $this->hasChildren(),
            'has_sub_categories' => $this->hasSubCategories(),
        ) + $this->getColumns();
    }
}
