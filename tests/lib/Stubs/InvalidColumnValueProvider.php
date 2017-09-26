<?php

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

class InvalidColumnValueProvider implements ColumnValueProviderInterface
{
    /**
     * Provides the column value.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return mixed
     */
    public function getValue(ItemInterface $item)
    {
        return null;
    }
}
