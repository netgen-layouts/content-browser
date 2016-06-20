<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ItemInterface
{
    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType();

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns the value.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ValueInterface
     */
    public function getValue();
}
