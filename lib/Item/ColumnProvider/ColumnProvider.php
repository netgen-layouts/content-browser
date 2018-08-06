<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
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
     * @var \Netgen\ContentBrowser\Config\Configuration
     */
    private $config;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface[]
     */
    private $columnValueProviders;

    /**
     * @param \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface $itemRenderer
     * @param \Netgen\ContentBrowser\Config\Configuration $config
     * @param \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface[] $columnValueProviders
     */
    public function __construct(
        ItemRendererInterface $itemRenderer,
        Configuration $config,
        array $columnValueProviders
    ) {
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
        $this->columnValueProviders = array_filter(
            $columnValueProviders,
            function (ColumnValueProviderInterface $columnValueProvider): bool {
                return true;
            }
        );
    }

    public function provideColumns(ItemInterface $item): array
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
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If value provider for the column does not exist
     */
    private function provideColumn(ItemInterface $item, array $columnConfig): string
    {
        if (isset($columnConfig['template'])) {
            return $this->itemRenderer->renderItem(
                $item,
                $columnConfig['template']
            );
        }

        if (!isset($this->columnValueProviders[$columnConfig['value_provider']])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Column value provider "%s" does not exist',
                    $columnConfig['value_provider']
                )
            );
        }

        $columnValue = $this
            ->columnValueProviders[$columnConfig['value_provider']]
            ->getValue($item);

        return $columnValue ?? '';
    }
}
