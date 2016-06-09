<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemReference;
use PHPUnit\Framework\TestCase;

class ItemReferenceTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemReference
     */
    protected $item;

    public function setUp()
    {
        $this->item = new ItemReference(
            array(
                'id' => 42,
                'name' => 'Item',
                'parentId' => 24,
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\AbstractItem::__construct
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testExceptionOnNonExistentParameter()
    {
        $item = new ItemReference(array('nonExistent' => 'value'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\AbstractItem::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemReference::getId
     */
    public function testGetId()
    {
        self::assertEquals(42, $this->item->getId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemReference::getName
     */
    public function testGetName()
    {
        self::assertEquals('Item', $this->item->getName());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemReference::getParentId
     */
    public function testGetParentId()
    {
        self::assertEquals(24, $this->item->getParentId());
    }
}
