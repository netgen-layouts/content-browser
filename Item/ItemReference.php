<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class ItemReference implements ItemReferenceInterface
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
     * Sets the item ID.
     *
     * @param int|string $id
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
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
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
