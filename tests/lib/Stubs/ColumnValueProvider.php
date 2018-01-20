<?php

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class ColumnValueProvider implements ColumnValueProviderInterface
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
        return 'some_value';
    }
}
