<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
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
     * Returns the item data.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    protected function getItemData(ItemInterface $item)
    {
        $configuredItem = $this->itemConfigurator->configureItem($item);

        $data = array(
            'value' => $item->getValue()->getId(),
            'parent_id' => $item->getParentId(),
            'name' => $item->getName(),
            'selectable' => $configuredItem->isSelectable(),
            'has_children' => false,
            'has_sub_categories' => false,
        ) + $this->columnProvider->provideColumns($item);

        if ($item instanceof CategoryInterface) {
            $data['id'] = $item->getId();
            $data['has_children'] = $this->itemRepository->getSubItemsCount($item) > 0;
            $data['has_sub_categories'] = $this->itemRepository->getSubCategoriesCount($item) > 0;
        }

        $data['html'] = $this->itemRenderer->renderItem($item, $configuredItem->getTemplate());

        return $data;
    }

    /**
     * Returns the category data.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return array
     */
    protected function getCategoryData(CategoryInterface $category)
    {
        $data = array(
            'id' => $category->getId(),
            'value' => null,
            'parent_id' => $category->getParentId(),
            'name' => $category->getName(),
            'selectable' => false,
            'has_children' => $this->itemRepository->getSubItemsCount($category) > 0,
            'has_sub_categories' => $this->itemRepository->getSubCategoriesCount($category) > 0,
        );

        if ($category instanceof ItemInterface) {
            $configuredItem = $this->itemConfigurator->configureItem($category);

            $data['value'] = $category->getValue()->getId();
            $data['selectable'] = $configuredItem->isSelectable();
        }

        return $data;
    }

    /**
     * Serializes the list of items to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[] $items
     *
     * @return array
     */
    public function serialize(array $items)
    {
        return array_map(
            function ($item) {
                if ($item instanceof CategoryInterface) {
                    return $this->getCategoryData($item);
                }

                return $this->getItemData($item);
            },
            $items
        );
    }
}
