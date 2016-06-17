<?php

namespace Netgen\Bundle\ContentBrowserBundle\Value\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Column\ColumnProviderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

class ValueSerializer implements ValueSerializerInterface
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
     * Serializes the value to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return array
     */
    public function serializeValue(ValueInterface $value)
    {
        $item = $this->itemBuilder->buildItem($value);

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
     * Serializes the list of values to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[] $values
     *
     * @return array
     */
    public function serializeValues(array $values)
    {
        return array_map(
            function (ValueInterface $value) {
                return $this->serializeValue($value);
            },
            $values
        );
    }
}
