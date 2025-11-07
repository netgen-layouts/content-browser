<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item;

interface LocationInterface
{
    /**
     * Returns the location ID.
     */
    public function getLocationId(): int|string;

    /**
     * Returns the name.
     */
    public function getName(): string;

    /**
     * Returns the parent ID. Parent ID is null if location has no parent.
     */
    public function getParentId(): int|string|null;
}
