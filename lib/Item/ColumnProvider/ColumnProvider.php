<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Psr\Container\ContainerInterface;

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
     * @var \Psr\Container\ContainerInterface
     */
    private $columnValueProviders;

    public function __construct(
        ItemRendererInterface $itemRenderer,
        Configuration $config,
        ContainerInterface $columnValueProviders
    ) {
        $this->itemRenderer = $itemRenderer;
        $this->config = $config;
        $this->columnValueProviders = $columnValueProviders;
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
     * @param mixed[] $columnConfig
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

        $columnValue = $this->getColumnValueProvider($columnConfig['value_provider'])->getValue($item);

        return $columnValue ?? '';
    }

    /**
     * Returns the column value provider with provided identifier from the collection.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If the column value provider does not exist or is not of correct type
     */
    private function getColumnValueProvider(string $identifier): ColumnValueProviderInterface
    {
        if (!$this->columnValueProviders->has($identifier)) {
            throw new InvalidArgumentException(sprintf('Column value provider "%s" does not exist.', $identifier));
        }

        $valueProvider = $this->columnValueProviders->get($identifier);
        if (!$valueProvider instanceof ColumnValueProviderInterface) {
            throw new InvalidArgumentException(sprintf('Column value provider "%s" does not exist.', $identifier));
        }

        return $valueProvider;
    }
}
