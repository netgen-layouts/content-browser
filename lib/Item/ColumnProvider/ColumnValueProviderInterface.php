<?php

namespace Netgen\ContentBrowser\Item\ColumnProvider;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ColumnValueProviderInterface
{
    /**
     * Provides the column value.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return mixed
     */
    public function getValue(ItemInterface $item);
}
