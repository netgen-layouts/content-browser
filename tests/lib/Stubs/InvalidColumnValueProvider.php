<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class InvalidColumnValueProvider implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item): ?string
    {
        return null;
    }
}
