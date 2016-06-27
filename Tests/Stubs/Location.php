<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;

class Location implements LocationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * Constructor.
     *
     * @param int $id
     * @param int $parentId
     */
    public function __construct($id, $parentId = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
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
