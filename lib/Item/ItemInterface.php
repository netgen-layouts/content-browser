<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item;

interface ItemInterface
{
    /**
     * Returns the value.
     */
    public function getValue(): int|string;

    /**
     * Returns the name.
     */
    public function getName(): string;

    /**
     * Returns if the item is visible.
     */
    public function isVisible(): bool;

    /**
     * Returns if the item is selectable.
     */
    public function isSelectable(): bool;
}
