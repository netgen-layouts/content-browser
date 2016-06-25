<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ConfiguredItemInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable();

    /**
     * Returns the item template.
     *
     * @return string
     */
    public function getTemplate();
}
