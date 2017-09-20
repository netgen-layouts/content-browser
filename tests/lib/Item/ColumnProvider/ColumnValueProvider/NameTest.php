<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider\ColumnValueProvider;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\Name;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\Name
     */
    private $provider;

    public function setUp()
    {
        $this->provider = new Name();
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProvider\Name::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(
            'This is a name',
            $this->provider->getValue(new Item())
        );
    }
}
