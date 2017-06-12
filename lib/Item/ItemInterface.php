<?php

namespace Netgen\ContentBrowser\Item;

interface ItemInterface
{
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
