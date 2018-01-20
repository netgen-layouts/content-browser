<?php

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;

final class Item implements ItemInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
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
