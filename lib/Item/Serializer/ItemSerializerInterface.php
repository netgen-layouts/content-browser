<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

interface ItemSerializerInterface
{
    /**
     * Serializes the item to array.
     *
     * @return array<string, mixed>
     */
    public function serializeItem(ItemInterface $item): array;

    /**
     * Serializes the location to array.
     *
     * @return array<string, mixed>
     */
    public function serializeLocation(LocationInterface $location): array;
}
