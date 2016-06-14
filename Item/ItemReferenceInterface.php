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
     * Returns the value type.
     *
     * @return int|string
     */
    public function getValueType();

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue();

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
}
