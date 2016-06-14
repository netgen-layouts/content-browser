<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class Item extends AbstractItem implements ItemInterface
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $valueType;

    /**
     * @var int|string
     */
    protected $value;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int|string
     */
    protected $parentId;

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
     * @var mixed
     */
    protected $object;

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
     * Returns the value type.
     *
     * @return int|string
     */
    public function getValueType()
    {
        return $this->valueType;
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
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Returns the object.
     *
     * @return bool
     */
    public function getObject()
    {
        return $this->object;
    }
}
