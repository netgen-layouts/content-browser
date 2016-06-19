<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;

class ColumnProvider implements ColumnProviderInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProviderInterface[]
     */
    protected $columnValueProviders = array();

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface $itemRenderer
     * @param array $config
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProviderInterface[] $columnValueProviders
     */
    public function __construct(
        ItemRendererInterface $itemRenderer,
        array $config,
        array $columnValueProviders = array()
    ) {
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
        $this->columnValueProviders = $columnValueProviders;
    }

    /**
     * Provides the columns for selected item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function provideColumns(ItemInterface $item)
    {
        $columns = array();

        foreach ($this->config['columns'] as $columnIdentifier => $columnConfig) {
            if (isset($columnConfig['template'])) {
                $columns[$columnIdentifier] = $this->itemRenderer->renderItem(
                    $item,
                    $columnConfig['template']
                );
            } else {
                $columns[$columnIdentifier] = $this
                    ->columnValueProviders[$columnConfig['value_provider']]
                    ->getValue($item);
            }
        }

        return $columns;
    }
}
