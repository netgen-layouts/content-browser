<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ItemReferenceInterface
{
    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();
    /**
     * Sets the item ID.
     *
     * @param int|string $id
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function setId($id);

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Sets the item parent ID.
     *
     * @param int|string $parentId
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function setParentId($parentId);

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the item name.
     *
     * @param string $name
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function setName($name);
}
