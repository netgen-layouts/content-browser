<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class Item implements ItemInterface
{
    /**
     * @var int
     */
    protected $parentId;

    /**
     * Constructor.
     *
     * @param int $parentId
     */
    public function __construct($parentId)
    {
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
    }

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return 'This is a name';
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
