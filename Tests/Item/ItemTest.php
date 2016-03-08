<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item;

use Netgen\Bundle\ContentBrowserBundle\Item\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::__construct
     */
    public function testSetProperties()
    {
        $item = new Item(
            array(
                'id' => 42,
                'parentId' => 84,
            )
        );

        self::assertEquals(42, $item->id);
        self::assertEquals(84, $item->parentId);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::__construct
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testSetNonExistingProperties()
    {
        $item = new Item(
            array(
                'someNonExistingProperty' => 42,
            )
        );
    }
}
