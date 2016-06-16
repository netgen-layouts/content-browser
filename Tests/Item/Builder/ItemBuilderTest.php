<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Builder;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilder;
use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\ConverterStub;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

class ItemBuilderTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->backendRegistry = new BackendRegistry();

        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->backendRegistry->addBackend('value', $this->backendMock);

        $this->builder = new ItemBuilder(
            $this->backendRegistry,
            array(
                'sections' => array(12, 13, 14),
                'category_types' => array('type'),
            ),
            array('value' => new ConverterStub())
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilder::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Builder\ItemBuilder::buildItem
     */
    public function testBuildItem()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('getChildrenCount')
            ->with(
                $this->equalTo(new Value(24, 23)),
                $this->equalTo(array('types' => array('type')))
            )
            ->will($this->returnValue(4));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getChildrenCount')
            ->with($this->equalTo(new Value(24, 23)))
            ->will($this->returnValue(3));

        self::assertEquals(
            new Item(
                array(
                    'id' => 24,
                    'valueType' => 'value',
                    'value' => 23,
                    'parentId' => 45,
                    'name' => 'This is a name',
                    'isSelectable' => true,
                    'hasChildren' => true,
                    'hasSubCategories' => true,
                    'valueObject' => new Value(24, 23),
                )
            ),
            $this->builder->buildItem(new Value(24, 23))
        );
    }
}
