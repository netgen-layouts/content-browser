<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class ColumnValueProvider implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item): string
    {
        return 'some_value';
    }
}
