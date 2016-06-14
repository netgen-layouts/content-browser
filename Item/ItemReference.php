<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class ItemReference extends AbstractItem implements ItemReferenceInterface
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
     * @var mixed
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
}
