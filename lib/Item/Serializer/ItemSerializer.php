<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class ItemSerializer implements ItemSerializerInterface
{
    public function __construct(
        private BackendInterface $backend,
        private ColumnProviderInterface $columnProvider,
    ) {}

    public function serializeItem(ItemInterface $item): array
    {
        $data = [
            'location_id' => null,
            'value' => $item->value,
            'name' => $item->name,
            'visible' => $item->isVisible,
            'selectable' => $item->isSelectable,
            'has_sub_items' => false,
            'columns' => $this->columnProvider->provideColumns($item),
        ];

        if ($item instanceof LocationInterface) {
            $data['location_id'] = $item->locationId;
            $data['has_sub_items'] = $this->backend->getSubItemsCount($item) > 0;
        }

        return $data;
    }

    public function serializeLocation(LocationInterface $location): array
    {
        return [
            'id' => $location->locationId,
            'parent_id' => $location->parentId,
            'name' => $location->name,
            'has_sub_items' => $this->backend->getSubItemsCount($location) > 0,
            'has_sub_locations' => $this->backend->getSubLocationsCount($location) > 0,
            // Used exclusively to display columns for parent location
            'visible' => true,
            'columns' => [
                'name' => $location->name,
            ],
        ];
    }
}
