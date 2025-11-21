<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item;

interface ItemInterface
{
    /**
     * Returns the value.
     */
    public int|string $value { get; }

    /**
     * Returns the name.
     */
    public string $name { get; }

    /**
     * Returns if the item is visible.
     */
    public bool $isVisible { get; }

    /**
     * Returns if the item is selectable.
     */
    public bool $isSelectable { get; }
}
