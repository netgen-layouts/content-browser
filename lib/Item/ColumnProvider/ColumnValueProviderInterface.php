<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ColumnValueProviderInterface
{
    /**
     * Provides the column value. Can return null if the provided item is unsupported.
     */
    public function getValue(ItemInterface $item): ?string;
}
