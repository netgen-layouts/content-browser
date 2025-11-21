<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item;

interface LocationInterface
{
    /**
     * Returns the location ID.
     */
    public int|string $locationId { get; }

    /**
     * Returns the name.
     */
    public string $name { get; }

    /**
     * Returns the parent ID. Parent ID is null if location has no parent.
     */
    public int|string|null $parentId { get; }
}
