<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Tree;

use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Netgen\Bundle\ContentBrowserBundle\Tree\Tree;
use Netgen\Bundle\ContentBrowserBundle\Adapter\AdapterInterface;

class TreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Tree\Tree
     */
    protected $tree;

    public function setUp()
    {
        $this->adapterMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tree = new Tree(
            $this->adapterMock,
            array(
                'root_items' => array(2, 43, 5),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::getRootItems
     */
    public function testGetRootItems()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::getItem
     */
    public function testGetItem()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::getItem
     * @ expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException
     */
    public function testGetItemThrowsNotFoundException()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::getItem
     * @ expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException
     */
    public function testGetItemThrowsOutOfBoundsException()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::getChildren
     */
    public function testGetChildren()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::hasChildren
     */
    public function testHasChildren()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::getCategories
     */
    public function testGetCategories()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::hasChildrenCategories
     */
    public function testHasChildrenCategories()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::isRootItem
     */
    public function testIsRootItem()
    {
        self::assertTrue(
            $this->tree->isRootItem(new Item(array('id' => 2)))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::isRootItem
     */
    public function testIsRootItemReturnsFalse()
    {
        self::assertFalse(
            $this->tree->isRootItem(new Item(array('id' => 22)))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::isInsideRootItems
     */
    public function testIsInsideRootItems()
    {
        self::assertTrue(
            $this->tree->isInsideRootItems(new Item(array('path' => array(2, 42, 84))))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Tree\Tree::isInsideRootItems
     */
    public function testIsInsideRootItemsReturnsFalse()
    {
        self::assertFalse(
            $this->tree->isInsideRootItems(new Item(array('path' => array(24, 42, 84))))
        );
    }
}
