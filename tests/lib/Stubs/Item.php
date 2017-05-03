<?php

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;

class Item implements ItemInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param int $parentId
     */
    public function __construct($value = null, $parentId = null)
    {
        $this->value = $value;
        $this->parentId = $parentId;
    }

    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType()
    {
        return 'value';
    }

    /**
     * Returns the item value.
     *
     * @return mixed
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
        $name = 'This is a name';

        if ($this->value !== null) {
            $name .= ' (' . $this->value . ')';
        }

        return $name;
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
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return true;
    }
}
