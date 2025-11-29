<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Psr\Container\ContainerInterface;

use function array_map;
use function sprintf;

final class ColumnProvider implements ColumnProviderInterface
{
    public function __construct(
        private ItemRendererInterface $itemRenderer,
        private Configuration $config,
        private ContainerInterface $columnValueProviders,
    ) {}

    public function provideColumns(ItemInterface $item): array
    {
        return array_map(
            fn (array $columnConfig): string => $this->provideColumn($item, $columnConfig),
            $this->config->getColumns(),
        );
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
                $columnConfig['template'],
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
