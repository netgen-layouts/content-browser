<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\Serializer;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializer;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use Netgen\ContentBrowser\Tests\Stubs\LocationItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemSerializer::class)]
final class ItemSerializerTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    private Stub&ColumnProviderInterface $columnProviderStub;

    private ItemSerializer $serializer;

    protected function setUp(): void
    {
        $this->backendStub = self::createStub(BackendInterface::class);
        $this->columnProviderStub = self::createStub(ColumnProviderInterface::class);

        $this->serializer = new ItemSerializer(
            $this->backendStub,
            $this->columnProviderStub,
        );
    }

    public function testSerializeItem(): void
    {
        $item = new LocationItem(84, 42);

        $this->backendStub
            ->method('getSubItemsCount')
            ->willReturn(3);

        $this->columnProviderStub
            ->method('provideColumns')
            ->willReturn(['column' => 'value']);

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
            $data,
        );
    }

    public function testSerializeNonLocationItem(): void
    {
        $item = new Item(84);

        $this->columnProviderStub
            ->method('provideColumns')
            ->willReturn(['column' => 'value']);

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
            $data,
        );
    }

    public function testSerializeLocation(): void
    {
        $location = new Location(42, 24);

        $this->backendStub
            ->method('getSubItemsCount')
            ->willReturn(3);

        $this->backendStub
            ->method('getSubLocationsCount')
            ->willReturn(4);

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
            $data,
        );
    }
}
