<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

class ItemLocation implements ItemInterface, LocationInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $parentId;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param string $name
     * @param mixed $parentId
     */
    public function __construct($value, $name, $parentId = null)
    {
        $this->value = $value;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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

    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getLocationId()
    {
        return $this->value;
    }

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}
