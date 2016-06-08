<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Builder;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Builder\Builder;
use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemReference;
use Netgen\Bundle\ContentBrowserBundle\Tests\Item\Converter\Stubs\ConverterStub;
use Twig_Environment;
use stdClass;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twigMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Builder\Builder
     */
    protected $builder;

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->twigMock = $this->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->builder = new Builder(
            new ConverterStub(),
            $this->backendMock,
            $this->twigMock,
            array(
                'root_items' => array(12, 13, 14),
                'category_types' => array('type'),
                'columns' => array(
                    'column1' => array(
                        'template' => 'template.html.twig',
                    ),
                    'column2' => array(),
                    'column3' => array(),
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Builder\Builder::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Builder\Builder::buildItem
     */
    public function testBuildItem()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('getChildrenCount')
            ->with($this->equalTo(24))
            ->will($this->returnValue(3));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getChildrenCount')
            ->with($this->equalTo(24), $this->equalTo(array('types' => array('type'))))
            ->will($this->returnValue(4));

        $this->twigMock
            ->expects($this->at(0))
            ->method('render')
            ->with($this->equalTo('template.html.twig'), $this->equalTo(array('var' => 'value')))
            ->will($this->returnValue('rendered column 1'));

        self::assertEquals(
            new Item(
                array(
                    'id' => 24,
                    'value' => 23,
                    'parentId' => 42,
                    'name' => 'Some item',
                    'isSelectable' => true,
                    'hasChildren' => true,
                    'hasSubCategories' => true,
                    'templateVariables' => array('var' => 'value'),
                    'columns' => array(
                        'column1' => 'rendered column 1',
                        'column2' => 'value2',
                        'column3' => '',
                    )
                )
            ),
            $this->builder->buildItem(new stdClass())
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Builder\Builder::buildItemReference
     */
    public function testBuildItemReference()
    {
        self::assertEquals(
            new ItemReference(
                array(
                    'id' => 24,
                    'name' => 'Some item',
                    'parentId' => 42,
                )
            ),
            $this->builder->buildItemReference(new stdClass())
        );
    }
}
