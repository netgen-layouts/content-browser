<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Serializer;

use Generator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class ItemSerializer implements ItemSerializerInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface
     */
    private $columnProvider;

    public function __construct(
        BackendInterface $backend,
        ColumnProviderInterface $columnProvider
    ) {
        $this->backend = $backend;
        $this->columnProvider = $columnProvider;
    }

    public function serializeItem(ItemInterface $item): array
    {
        $data = [
            'location_id' => null,
            'value' => $item->getValue(),
            'name' => $item->getName(),
            'visible' => $item->isVisible(),
            'selectable' => $item->isSelectable(),
            'has_sub_items' => false,
            'columns' => $this->columnProvider->provideColumns($item),
        ];

        if ($item instanceof LocationInterface) {
            $data['location_id'] = $item->getLocationId();
            $data['has_sub_items'] = $this->backend->getSubItemsCount($item) > 0;
        }

        return $data;
    }

    public function serializeLocation(LocationInterface $location): array
    {
        return [
            'id' => $location->getLocationId(),
            'parent_id' => $location->getParentId(),
            'name' => $location->getName(),
            'has_sub_items' => $this->backend->getSubItemsCount($location) > 0,
            'has_sub_locations' => $this->backend->getSubLocationsCount($location) > 0,
            // Used exclusively to display columns for parent location
            'columns' => [
                'name' => $location->getName(),
            ],
        ];
    }

    public function serializeItems(iterable $items): Generator
    {
        foreach ($items as $item) {
            yield $this->serializeItem($item);
        }
    }

    public function serializeLocations(iterable $locations): Generator
    {
        foreach ($locations as $location) {
            yield $this->serializeLocation($location);
        }
    }
}
