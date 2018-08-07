<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\Serializer;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializer;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use Netgen\ContentBrowser\Tests\Stubs\LocationItem;
use PHPUnit\Framework\TestCase;

final class ItemSerializerTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $columnProviderMock;

    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface
     */
    private $serializer;

    public function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->columnProviderMock = $this->createMock(ColumnProviderInterface::class);

        $this->serializer = new ItemSerializer(
            $this->backendMock,
            $this->columnProviderMock
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::__construct
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeItem
     */
    public function testSerializeItem(): void
    {
        $item = new LocationItem(84, 42);

        $this->backendMock
            ->expects(self::once())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($item))
            ->will(self::returnValue(3));

        $this->columnProviderMock
            ->expects(self::once())
            ->method('provideColumns')
            ->with(self::identicalTo($item))
            ->will(self::returnValue(['column' => 'value']));

        $data = $this->serializer->serializeItem($item);

        self::assertSame(
            [
                'location_id' => 42,
                'value' => 84,
                'name' => 'This is a name (84)',
                'visible' => true,
                'selectable' => true,
                'has_sub_items' => true,
                'columns' => [
                    'column' => 'value',
                ],
            ],
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::__construct
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeItem
     */
    public function testSerializeNonLocationItem(): void
    {
        $item = new Item(84);

        $this->backendMock
            ->expects(self::never())
            ->method('getSubItemsCount');

        $this->columnProviderMock
            ->expects(self::once())
            ->method('provideColumns')
            ->with(self::identicalTo($item))
            ->will(self::returnValue(['column' => 'value']));

        $data = $this->serializer->serializeItem($item);

        self::assertSame(
            [
                'location_id' => null,
                'value' => 84,
                'name' => 'This is a name (84)',
                'visible' => true,
                'selectable' => true,
                'has_sub_items' => false,
                'columns' => [
                    'column' => 'value',
                ],
            ],
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeLocation
     */
    public function testSerializeLocation(): void
    {
        $location = new Location(42, 24);

        $this->backendMock
            ->expects(self::at(0))
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->will(self::returnValue(3));

        $this->backendMock
            ->expects(self::at(1))
            ->method('getSubLocationsCount')
            ->with(self::identicalTo($location))
            ->will(self::returnValue(4));

        $data = $this->serializer->serializeLocation($location);

        self::assertSame(
            [
                'id' => 42,
                'parent_id' => 24,
                'name' => 'This is a name',
                'has_sub_items' => true,
                'has_sub_locations' => true,
                'visible' => true,
                'columns' => [
                    'name' => 'This is a name',
                ],
            ],
            $data
        );
    }
}
