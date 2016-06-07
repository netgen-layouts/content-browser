<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class ItemReference extends AbstractItem implements ItemReferenceInterface
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $name;

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
}
