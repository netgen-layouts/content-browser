<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\Name;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Name::class)]
final class NameTest extends TestCase
{
    private Name $provider;

    protected function setUp(): void
    {
        $this->provider = new Name();
    }

    public function testGetValue(): void
    {
        self::assertSame(
            'This is a name (42)',
            $this->provider->getValue(new Item(42)),
        );
    }
}
