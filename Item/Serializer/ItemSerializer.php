<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Column\ColumnProviderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class ItemSerializer implements ItemSerializerInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Column\ColumnProviderInterface
     */
    protected $columnProvider;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Column\ColumnProviderInterface $columnProvider
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilderInterface $itemBuilder
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface $itemRenderer
     * @param array $config
     */
    public function __construct(
        ColumnProviderInterface $columnProvider,
        ItemBuilderInterface $itemBuilder,
        ItemRendererInterface $itemRenderer,
        array $config
    ) {
        $this->columnProvider = $columnProvider;
        $this->itemBuilder = $itemBuilder;
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
    }

    /**
     * Serializes the item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function serializeItem(ItemInterface $item)
    {
        $data = array(
            'id' => $item->getId(),
            'value' => $item->getValue(),
            'parent_id' => $item->getParentId(),
            'name' => $item->getName(),
            'selectable' => $item->isSelectable(),
            'has_children' => $item->hasChildren(),
            'has_sub_categories' => $item->hasSubCategories(),
        ) + $this->columnProvider->provideColumns($item);

        $data['html'] = $this->itemRenderer->renderItem($item, $this->config['template']);

        return $data;
    }

    /**
     * Builds items from specified values and serializes them to an array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[] $values
     *
     * @return array
     */
    public function serializeValues(array $values)
    {
        return array_map(
            function ($value) {
                return $this->serializeItem(
                    $this->itemBuilder->buildItem($value)
                );
            },
            $values
        );
    }
}
