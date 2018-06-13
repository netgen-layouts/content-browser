<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ColumnProviderInterface
{
    /**
     * Provides the columns for selected item.
     */
    public function provideColumns(ItemInterface $item): array;
}
