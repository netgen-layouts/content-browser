<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\Name;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{
    private Name $provider;

    protected function setUp(): void
    {
        $this->provider = new Name();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\Name::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame(
            'This is a name',
            $this->provider->getValue(new Item())
        );
    }
}
