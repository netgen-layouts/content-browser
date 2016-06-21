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
            'category_id' => null,
            'value' => $item->getValue(),
            'parent_id' => $item->getParentId(),
            'name' => $item->getName(),
            'visible' => $item->isVisible(),
            'selectable' => $configuredItem->isSelectable(),
            'has_children' => false,
        ) + $this->columnProvider->provideColumns($item);

        if ($item instanceof CategoryInterface) {
            $data['category_id'] = $item->getId();
            $data['has_children'] = $this->itemRepository->getSubItemsCount($item) > 0;
        }

        $data['html'] = $this->itemRenderer->renderItem($item, $configuredItem->getTemplate());

        return $data;
    }

    /**
     * Serializes the category to array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return array
     */
    public function serializeCategory(CategoryInterface $category)
    {
        return array(
            'id' => $category->getId(),
            'parent_id' => $category->getParentId(),
            'name' => $category->getName(),
            'has_children' => $this->itemRepository->getSubItemsCount($category) > 0,
            'has_sub_categories' => $this->itemRepository->getSubCategoriesCount($category) > 0,
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface[] $categories
     *
     * @return array
     */
    public function serializeCategories(array $categories)
    {
        return array_map(
            function (CategoryInterface $category) {
                return $this->serializeCategory($category);
            },
            $categories
        );
    }
}
