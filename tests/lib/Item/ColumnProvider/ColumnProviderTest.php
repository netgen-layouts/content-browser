<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Tests\Stubs\ColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\InvalidColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class ColumnProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemRendererMock;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $config;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider
     */
    private $columnProvider;

    public function setUp()
    {
        $this->itemRendererMock = $this->createMock(ItemRendererInterface::class);

        $this->config = new Configuration(
            'value',
            array(
                'columns' => array(
                    'column1' => array(
                        'value_provider' => 'provider',
                    ),
                    'column2' => array(
                        'value_provider' => 'invalid',
                    ),
                ),
            )
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array(
                'provider' => new ColumnValueProvider(),
                'invalid' => new InvalidColumnValueProvider(),
            )
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::__construct
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Column value provider "provider" does not exist
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
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
     */
    public function testProvideColumns()
    {
        $this->assertEquals(
            array('column1' => 'some_value', 'column2' => ''),
            $this->columnProvider->provideColumns(new Item())
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
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

        $this->assertEquals(
            array('column' => 'rendered column'),
            $this->columnProvider->provideColumns(new Item())
        );
    }
}
