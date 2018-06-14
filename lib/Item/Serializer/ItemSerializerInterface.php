<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Serializer;

use Generator;
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

    /**
     * Serializes the list of items.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface[] $items
     *
     * @return \Generator
     */
    public function serializeItems(iterable $items): Generator;

    /**
     * Serializes the list of items.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface[] $locations
     *
     * @return \Generator
     */
    public function serializeLocations(iterable $locations): Generator;
}
