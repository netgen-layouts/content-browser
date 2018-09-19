<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item;

interface LocationInterface
{
    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getLocationId();

    /**
     * Returns the name.
     */
    public function getName(): string;

    /**
     * Returns the parent ID. Parent ID is null if location has no parent.
     *
     * @return int|string|null
     */
    public function getParentId();
}
