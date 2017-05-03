<?php

namespace Netgen\ContentBrowser\Item;

interface ItemInterface
{
    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType();

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible();

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable();
}
