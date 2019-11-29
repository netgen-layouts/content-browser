<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ColumnProviderInterface
{
    /**
     * Provides the columns for selected item.
     *
     * @return array<string, string>
     */
    public function provideColumns(ItemInterface $item): array;
}
