<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\ColumnProvider;

use Netgen\Bundle\ContentBrowserBundle\Config\Configuration;
use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\ColumnValueProvider;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class ColumnProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRendererMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider
     */
    protected $columnProvider;

    public function setUp()
    {
        $this->itemRendererMock = $this->createMock(ItemRendererInterface::class);

        $this->config = new Configuration(
            'value',
            array(
                'columns' => array(
                    'column' => array(
                        'value_provider' => 'provider',
                    ),
                ),
            )
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array('provider' => new ColumnValueProvider())
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider::__construct
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentException()
    {
        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array('other' => new ColumnValueProvider())
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider::provideColumns
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider::provideColumn
     */
    public function testProvideColumns()
    {
        self::assertEquals(
            array('column' => 'some_value'),
            $this->columnProvider->provideColumns(new Item())
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider::provideColumns
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProvider::provideColumn
     */
    public function testProvideColumnsWithTemplate()
    {
        $this->config = new Configuration(
            'value',
            array(
                'columns' => array(
                    'column' => array(
                        'template' => 'template.html.twig',
                    ),
                ),
            )
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array()
        );

        $this->itemRendererMock
            ->expects($this->once())
            ->method('renderItem')
            ->with($this->equalTo(new Item()), $this->equalTo('template.html.twig'))
            ->will($this->returnValue('rendered column'));

        self::assertEquals(
            array('column' => 'rendered column'),
            $this->columnProvider->provideColumns(new Item())
        );
    }
}
