<?php

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;

class ItemSerializer implements ItemSerializerInterface
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface
     */
    protected $columnProvider;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Backend\BackendInterface $backend
     * @param \Netgen\ContentBrowser\Config\ConfigurationInterface $config
     * @param \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface $columnProvider
     * @param \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface $itemRenderer
     */
    public function __construct(
        BackendInterface $backend,
        ConfigurationInterface $config,
        ColumnProviderInterface $columnProvider,
        ItemRendererInterface $itemRenderer
    ) {
        $this->backend = $backend;
        $this->config = $config;
        $this->columnProvider = $columnProvider;
        $this->itemRenderer = $itemRenderer;
    }

    /**
     * Serializes the item to array.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item)
    {
        $data = array(
            'location_id' => null,
            'value' => $item->getValue(),
            'parent_location_id' => $item->getParentId(),
            'name' => $item->getName(),
            'visible' => $item->isVisible(),
            'selectable' => $item->isSelectable(),
            'has_sub_items' => false,
            'columns' => $this->columnProvider->provideColumns($item),
        );

        if ($item instanceof LocationInterface) {
            $data['location_id'] = $item->getLocationId();
            $data['has_sub_items'] = $this->backend->getSubItemsCount($item) > 0;
        }

        if ($this->config->hasPreview()) {
            $data['html'] = $this->itemRenderer->renderItem($item, $this->config->getTemplate());
        }

        return $data;
    }

    /**
     * Serializes the location to array.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return array
     */
    public function serializeLocation(LocationInterface $location)
    {
        return array(
            'id' => $location->getLocationId(),
            'parent_id' => $location->getParentId(),
            'name' => $location->getName(),
            'has_sub_items' => $this->backend->getSubItemsCount($location) > 0,
            'has_sub_locations' => $this->backend->getSubLocationsCount($location) > 0,
            // Used exclusively to display columns for parent location
            'columns' => array(
                'name' => $location->getName(),
            ),
        );
    }

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface[] $items
     *
     * @return array
     */
    public function serializeItems(array $items)
    {
        return array_map(
            function (ItemInterface $item) {
                return $this->serializeItem($item);
            },
            $items
        );
    }

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface[] $locations
     *
     * @return array
     */
    public function serializeLocations(array $locations)
    {
        return array_map(
            function (LocationInterface $location) {
                return $this->serializeLocation($location);
            },
            $locations
        );
    }
}
