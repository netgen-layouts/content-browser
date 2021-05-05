<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Tests\Stubs\ColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\Container;
use Netgen\ContentBrowser\Tests\Stubs\InvalidColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ColumnProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    private MockObject $itemRendererMock;

    private Configuration $config;

    private ColumnProvider $columnProvider;

    protected function setUp(): void
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
            [],
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            new Container(
                [
                    'provider' => new ColumnValueProvider(),
                    'invalid' => new InvalidColumnValueProvider(),
                ],
            ),
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
            $this->columnProvider->provideColumns(new Item(42)),
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
            ],
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            new Container(),
        );

        $item = new Item(42);

        $this->itemRendererMock
            ->expects(self::once())
            ->method('renderItem')
            ->with(self::identicalTo($item), self::identicalTo('template.html.twig'))
            ->willReturn('rendered column');

        self::assertSame(
            ['column' => 'rendered column'],
            $this->columnProvider->provideColumns($item),
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
            new Container(['other' => new ColumnValueProvider()]),
        );

        $this->columnProvider->provideColumns(new Item(42));
    }
}
