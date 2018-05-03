<?php

namespace Netgen\ContentBrowser\Tests\Item\Serializer;

use DateTimeImmutable;
use DateTimeZone;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\EzPublish\Item;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializer;
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

    public function setUp()
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
    public function testSerializeItem()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->columnProviderMock
            ->expects($this->once())
            ->method('provideColumns')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(['column' => 'value']));

        $item = $this->getItem();

        $data = $this->serializer->serializeItem($item);

        $this->assertEquals(
            [
                'location_id' => 42,
                'value' => 84,
                'name' => 'Some name',
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
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeItems
     */
    public function testSerializeItems()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->columnProviderMock
            ->expects($this->once())
            ->method('provideColumns')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(['column' => 'value']));

        $item = $this->getItem();

        $data = $this->serializer->serializeItems([$item]);

        $this->assertEquals(
            [
                [
                    'location_id' => 42,
                    'value' => 84,
                    'name' => 'Some name',
                    'visible' => true,
                    'selectable' => true,
                    'has_sub_items' => true,
                    'columns' => [
                        'column' => 'value',
                    ],
                ],
            ],
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeLocation
     */
    public function testSerializeLocation()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getSubLocationsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(4));

        $item = $this->getItem();

        $data = $this->serializer->serializeLocation($item);

        $this->assertEquals(
            [
                'id' => 42,
                'parent_id' => 24,
                'name' => 'Some name',
                'has_sub_items' => true,
                'has_sub_locations' => true,
                'columns' => [
                    'name' => 'Some name',
                ],
            ],
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeLocations
     */
    public function testSerializeLocations()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getSubLocationsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(4));

        $item = $this->getItem();

        $data = $this->serializer->serializeLocations([$item]);

        $this->assertEquals(
            [
                [
                    'id' => 42,
                    'parent_id' => 24,
                    'name' => 'Some name',
                    'has_sub_items' => true,
                    'has_sub_locations' => true,
                    'columns' => [
                        'name' => 'Some name',
                    ],
                ],
            ],
            $data
        );
    }

    private function getItem()
    {
        $modificationDate = new DateTimeImmutable();
        $modificationDate = $modificationDate->setTimestamp(0);
        $modificationDate = $modificationDate->setTimezone(new DateTimeZone('UTC'));

        $publishedDate = new DateTimeImmutable();
        $publishedDate = $publishedDate->setTimestamp(10);
        $publishedDate = $publishedDate->setTimezone(new DateTimeZone('UTC'));

        $contentInfo = new ContentInfo(
            [
                'id' => 84,
                'contentTypeId' => 85,
                'ownerId' => 14,
                'sectionId' => 2,
                'modificationDate' => $modificationDate,
                'publishedDate' => $publishedDate,
            ]
        );

        $content = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => $contentInfo,
                    ]
                ),
            ]
        );

        $location = new Location(
            [
                'id' => 42,
                'parentLocationId' => 24,
                'invisible' => false,
                'priority' => 3,
                'contentInfo' => $contentInfo,
            ]
        );

        return new Item($location, $content, 84, 'Some name');
    }
}
