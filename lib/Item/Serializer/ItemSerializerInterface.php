<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

interface ItemSerializerInterface
{
    /**
     * Serializes the item to array.
     */
    public function serializeItem(ItemInterface $item): array;

    /**
     * Serializes the location to array.
     */
    public function serializeLocation(LocationInterface $location): array;
}
