<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;

final class ColumnProvider implements ColumnProviderInterface
{
    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    private $itemRenderer;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $config;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface[]
     */
    private $columnValueProviders = [];

    /**
     * @param \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface $itemRenderer
     * @param \Netgen\ContentBrowser\Config\ConfigurationInterface $config
     * @param \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface[] $columnValueProviders
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If value provider for one of the columns does not exist
     */
    public function __construct(
        ItemRendererInterface $itemRenderer,
        ConfigurationInterface $config,
        array $columnValueProviders = []
    ) {
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
        $this->columnValueProviders = $columnValueProviders;

        foreach ($this->config->getColumns() as $columnConfig) {
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

    public function provideColumns(ItemInterface $item)
    {
        $columns = [];

        foreach ($this->config->getColumns() as $columnIdentifier => $columnConfig) {
            $columns[$columnIdentifier] = $this->provideColumn($item, $columnConfig);
        }

        return $columns;
    }

    /**
     * Provides the column with specified identifier for selected item.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     * @param array $columnConfig
     *
     * @return string
     */
    private function provideColumn(ItemInterface $item, array $columnConfig)
    {
        if (isset($columnConfig['template'])) {
            return $this->itemRenderer->renderItem(
                $item,
                $columnConfig['template']
            );
        }

        $columnValue = $this
            ->columnValueProviders[$columnConfig['value_provider']]
            ->getValue($item);

        return $columnValue ?? '';
    }
}
