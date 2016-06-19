<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfiguratorInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ValueInterface;

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
     * Serializes the value to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item)
    {
        $configuredItem = $this->itemConfigurator->configureItem($item);

        $data = array(
            'id' => $configuredItem->getId(),
            'value' => $configuredItem->getValue() instanceof ValueInterface ?
                $configuredItem->getValue()->getId() :
                null,
            'parent_id' => $configuredItem->getParentId(),
            'name' => $configuredItem->getName(),
            'selectable' => $configuredItem->isSelectable(),
            'has_children' => $this->itemRepository->getChildrenCount($configuredItem) > 0,
            'has_sub_categories' => $this->itemRepository->getSubCategoriesCount($configuredItem) > 0,
        ) + $this->columnProvider->provideColumns($configuredItem);

        $data['html'] = $this->itemRenderer->renderItem($configuredItem, $configuredItem->getTemplate());

        return $data;
    }

    /**
     * Serializes the list of values to the array.
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
}
