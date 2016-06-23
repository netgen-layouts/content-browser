<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfiguratorInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class ItemSerializer implements ItemSerializerInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfiguratorInterface
     */
    protected $itemConfigurator;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface
     */
    protected $columnProvider;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfiguratorInterface $itemConfigurator
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface $columnProvider
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface $itemRenderer
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        ItemConfiguratorInterface $itemConfigurator,
        ColumnProviderInterface $columnProvider,
        ItemRendererInterface $itemRenderer
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemConfigurator = $itemConfigurator;
        $this->columnProvider = $columnProvider;
        $this->itemRenderer = $itemRenderer;
    }

    /**
     * Serializes the item to array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item)
    {
        $configuredItem = $this->itemConfigurator->configureItem($item);

        $data = array(
            'location_id' => null,
            'value' => $item->getValue(),
            'parent_location_id' => $item->getParentId(),
            'name' => $item->getName(),
            'visible' => $item->isVisible(),
            'selectable' => $configuredItem->isSelectable(),
            'has_sub_items' => false,
            'columns' => $this->columnProvider->provideColumns($item),
        );

        if ($item instanceof LocationInterface) {
            $data['location_id'] = $item->getId();
            $data['has_sub_items'] = $this->itemRepository->getSubItemsCount($item) > 0;
        }

        $data['html'] = $this->itemRenderer->renderItem($item, $configuredItem->getTemplate());

        return $data;
    }

    /**
     * Serializes the location to array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return array
     */
    public function serializeLocation(LocationInterface $location)
    {
        return array(
            'id' => $location->getId(),
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[] $items
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[] $locations
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
