<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item;

use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Item
     */
    protected $item;

    public function setUp()
    {
        $this->item = new Item(
            array(
                'id' => 42,
                'valueType' => 'value',
                'value' => 43,
                'name' => 'Item',
                'parentId' => 24,
                'isSelectable' => true,
                'hasChildren' => true,
                'hasSubCategories' => true,
                'valueObject' => new Value(24, 23),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\AbstractItem::__construct
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testExceptionOnNonExistentParameter()
    {
        $item = new Item(array('nonExistent' => 'value'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\AbstractItem::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::getId
     */
    public function testGetId()
    {
        self::assertEquals(42, $this->item->getId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('value', $this->item->getValueType());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(43, $this->item->getValue());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::getParentId
     */
    public function testGetParentId()
    {
        self::assertEquals(24, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::getName
     */
    public function testGetName()
    {
        self::assertEquals('Item', $this->item->getName());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::isSelectable
     */
    public function testIsSelectable()
    {
        self::assertTrue($this->item->isSelectable());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::hasChildren
     */
    public function testHasChildren()
    {
        self::assertTrue($this->item->hasChildren());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::hasSubCategories
     */
    public function testHasSubCategories()
    {
        self::assertTrue($this->item->hasSubCategories());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Item::getValueObject
     */
    public function testGetValueObject()
    {
        self::assertEquals(new Value(24, 23), $this->item->getValueObject());
    }
}
