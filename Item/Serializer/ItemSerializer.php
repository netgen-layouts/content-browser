<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface;
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
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface
     */
    protected $columnProvider;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\HandlerInterface[]
     */
    protected $itemHandlers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface $columnProvider
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface $itemRenderer
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface $config
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\HandlerInterface[] $itemHandlers
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        ColumnProviderInterface $columnProvider,
        ItemRendererInterface $itemRenderer,
        ConfigurationInterface $config,
        array $itemHandlers = array()
    ) {
        $this->itemRepository = $itemRepository;
        $this->columnProvider = $columnProvider;
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
        $this->itemHandlers = $itemHandlers;
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
        $itemHandler = $this->itemHandlers[$item->getType()];

        $data = array(
            'location_id' => null,
            'value' => $item->getValue(),
            'parent_location_id' => $item->getParentId(),
            'name' => $item->getName(),
            'visible' => $item->isVisible(),
            'selectable' => $itemHandler->isSelectable($item),
            'has_sub_items' => false,
            'columns' => $this->columnProvider->provideColumns($item),
        );

        if ($item instanceof LocationInterface) {
            $data['location_id'] = $item->getId();
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
