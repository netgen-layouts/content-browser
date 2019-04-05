<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Tests\Stubs\ColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\InvalidColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

final class ColumnProviderTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $itemRendererMock;

    /**
     * @var \Netgen\ContentBrowser\Config\Configuration
     */
    private $config;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider
     */
    private $columnProvider;

    public function setUp(): void
    {
        $this->itemRendererMock = $this->createMock(ItemRendererInterface::class);

        $this->config = new Configuration(
            'value',
            'Value',
            [
                'columns' => [
                    'column1' => [
                        'value_provider' => 'provider',
                    ],
                    'column2' => [
                        'value_provider' => 'invalid',
                    ],
                ],
            ],
            []
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            [
                'provider' => new ColumnValueProvider(),
                'invalid' => new InvalidColumnValueProvider(),
            ]
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::__construct
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     */
    public function testProvideColumns(): void
    {
        self::assertSame(
            ['column1' => 'some_value', 'column2' => ''],
            $this->columnProvider->provideColumns(new Item())
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     */
    public function testProvideColumnsWithTemplate(): void
    {
        $this->config = new Configuration(
            'value',
            'Value',
            [
                'columns' => [
                    'column' => [
                        'template' => 'template.html.twig',
                    ],
                ],
            ]
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            []
        );

        $item = new Item();

        $this->itemRendererMock
            ->expects(self::once())
            ->method('renderItem')
            ->with(self::identicalTo($item), self::identicalTo('template.html.twig'))
            ->willReturn('rendered column');

        self::assertSame(
            ['column' => 'rendered column'],
            $this->columnProvider->provideColumns($item)
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     */
    public function testProvideColumnsThrowsInvalidArgumentExceptionWithNoProvider(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Column value provider "provider" does not exist');

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            ['other' => new ColumnValueProvider()]
        );

        $this->columnProvider->provideColumns(new Item());
    }
}
