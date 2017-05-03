<?php

namespace Netgen\ContentBrowser\Item\Serializer;

use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;

class ItemSerializer implements ItemSerializerInterface
{
    /**
     * @var \Netgen\ContentBrowser\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface
     */
    protected $columnProvider;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface $columnProvider
     * @param \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface $itemRenderer
     * @param \Netgen\ContentBrowser\Config\ConfigurationInterface $config
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        ColumnProviderInterface $columnProvider,
        ItemRendererInterface $itemRenderer,
        ConfigurationInterface $config
    ) {
        $this->itemRepository = $itemRepository;
        $this->columnProvider = $columnProvider;
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
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
            $data['has_sub_items'] = $this->itemRepository->getSubItemsCount($item) > 0;
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
            'has_sub_items' => $this->itemRepository->getSubItemsCount($location) > 0,
            'has_sub_locations' => $this->itemRepository->getSubLocationsCount($location) > 0,
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
