<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Name implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item): ?string
    {
        return $item->getName();
    }
}
