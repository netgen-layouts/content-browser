<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class Item extends AbstractItem implements ItemInterface
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
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
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
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Returns if the item has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->hasChildren;
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
     * Returns the item template variables.
     *
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables;
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
