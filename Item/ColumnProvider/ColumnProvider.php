<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;

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
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If value provider for one of the columns does not exist
     */
    public function __construct(
        ItemRendererInterface $itemRenderer,
        array $config,
        array $columnValueProviders = array()
    ) {
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
        $this->columnValueProviders = $columnValueProviders;

        foreach ($this->config['columns'] as $columnIdentifier => $columnConfig) {
            if (isset($columnConfig['value_provider'])) {
                if (!isset($this->columnValueProviders[$columnConfig['value_provider']])) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Column value provider "%s" does not exist',
                            $columnConfig['value_provider']
                        )
                    );
                }
            }
        }
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
            $columns[$columnIdentifier] = $this->provideColumn($item, $columnConfig);
        }

        return $columns;
    }

    /**
     * Provides the column with specified identifier for selected item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param array $columnConfig
     *
     * @return array
     */
    protected function provideColumn(ItemInterface $item, array $columnConfig)
    {
        if (isset($columnConfig['template'])) {
            return $this->itemRenderer->renderItem(
                $item,
                $columnConfig['template']
            );
        }

        return $this
            ->columnValueProviders[$columnConfig['value_provider']]
            ->getValue($item);
    }
}
