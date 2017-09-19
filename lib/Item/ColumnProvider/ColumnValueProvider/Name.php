<?php

namespace Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

class Name implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item)
    {
        return $item->getName();
    }
}
