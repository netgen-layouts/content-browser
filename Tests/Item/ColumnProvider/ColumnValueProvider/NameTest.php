<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\ColumnProvider\ColumnValueProvider;

use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProvider\Name;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProvider\Name
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new Name();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnValueProvider\Name::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(
            'This is a name',
            $this->provider->getValue(new Item())
        );
    }
}
