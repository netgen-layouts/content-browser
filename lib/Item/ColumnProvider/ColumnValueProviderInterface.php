<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ColumnValueProviderInterface
{
    /**
     * Provides the column value. Can return null if the provided item is unsupported.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return string|null
     */
    public function getValue(ItemInterface $item);
}
