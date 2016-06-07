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
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();
}
