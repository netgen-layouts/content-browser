<?php

namespace Netgen\ContentBrowser\Tests\Item\Serializer;

use DateTime;
use DateTimeZone;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\ContentBrowser\Item\EzContent\Item;
use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializer;
use Netgen\ContentBrowser\Tests\Stubs\ItemSerializerHandler;
use PHPUnit\Framework\TestCase;

class ItemSerializerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $columnProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRendererMock;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface
     */
    protected $serializer;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);
        $this->columnProviderMock = $this->createMock(ColumnProviderInterface::class);
        $this->itemRendererMock = $this->createMock(ItemRendererInterface::class);

        $this->config = new Configuration(
            'ezcontent',
            array(
                'preview' => array(
                    'enabled' => true,
                    'template' => 'template.html.twig',
                ),
            )
        );

        $this->serializer = new ItemSerializer(
            $this->itemRepositoryMock,
            $this->columnProviderMock,
            $this->itemRendererMock,
            $this->config,
            array('ezcontent' => new ItemSerializerHandler())
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::__construct
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeItem
     */
    public function testSerializeItem()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->itemRendererMock
            ->expects($this->once())
            ->method('renderItem')
            ->with($this->equalTo($this->getItem()), $this->equalTo('template.html.twig'))
            ->will($this->returnValue('rendered item'));

        $this->columnProviderMock
            ->expects($this->once())
            ->method('provideColumns')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(array('column' => 'value')));

        $item = $this->getItem();

        $data = $this->serializer->serializeItem($item);

        $this->assertEquals(
            array(
                'location_id' => 42,
                'value' => 84,
                'parent_location_id' => 24,
                'name' => 'Some name',
                'visible' => true,
                'selectable' => true,
                'has_sub_items' => true,
                'columns' => array(
                    'column' => 'value',
                ),
                'html' => 'rendered item',
            ),
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::__construct
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeItems
     */
    public function testSerializeItems()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->itemRendererMock
            ->expects($this->once())
            ->method('renderItem')
            ->with($this->equalTo($this->getItem()), $this->equalTo('template.html.twig'))
            ->will($this->returnValue('rendered item'));

        $this->columnProviderMock
            ->expects($this->once())
            ->method('provideColumns')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(array('column' => 'value')));

        $item = $this->getItem();

        $data = $this->serializer->serializeItems(array($item));

        $this->assertEquals(
            array(
                array(
                    'location_id' => 42,
                    'value' => 84,
                    'parent_location_id' => 24,
                    'name' => 'Some name',
                    'visible' => true,
                    'selectable' => true,
                    'has_sub_items' => true,
                    'columns' => array(
                        'column' => 'value',
                    ),
                    'html' => 'rendered item',
                ),
            ),
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeLocation
     */
    public function testSerializeLocation()
    {
        $this->itemRepositoryMock
            ->expects($this->at(0))
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->itemRepositoryMock
            ->expects($this->at(1))
            ->method('getSubLocationsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(4));

        $item = $this->getItem();

        $data = $this->serializer->serializeLocation($item);

        $this->assertEquals(
            array(
                'id' => 42,
                'parent_id' => 24,
                'name' => 'Some name',
                'has_sub_items' => true,
                'has_sub_locations' => true,
                'columns' => array(
                    'name' => 'Some name',
                ),
            ),
            $data
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Serializer\ItemSerializer::serializeLocations
     */
    public function testSerializeLocations()
    {
        $this->itemRepositoryMock
            ->expects($this->at(0))
            ->method('getSubItemsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(3));

        $this->itemRepositoryMock
            ->expects($this->at(1))
            ->method('getSubLocationsCount')
            ->with($this->equalTo($this->getItem()))
            ->will($this->returnValue(4));

        $item = $this->getItem();

        $data = $this->serializer->serializeLocations(array($item));

        $this->assertEquals(
            array(
                array(
                    'id' => 42,
                    'parent_id' => 24,
                    'name' => 'Some name',
                    'has_sub_items' => true,
                    'has_sub_locations' => true,
                    'columns' => array(
                        'name' => 'Some name',
                    ),
                ),
            ),
            $data
        );
    }

    /**
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    protected function getItem()
    {
        $modificationDate = new DateTime();
        $modificationDate->setTimestamp(0);
        $modificationDate->setTimezone(new DateTimeZone('UTC'));

        $publishedDate = new DateTime();
        $publishedDate->setTimestamp(10);
        $publishedDate->setTimezone(new DateTimeZone('UTC'));

        $contentInfo = new ContentInfo(
            array(
                'id' => 84,
                'contentTypeId' => 85,
                'ownerId' => 14,
                'sectionId' => 2,
                'modificationDate' => $modificationDate,
                'publishedDate' => $publishedDate,
            )
        );

        $location = new Location(
            array(
                'id' => 42,
                'parentLocationId' => 24,
                'invisible' => false,
                'priority' => 3,
                'contentInfo' => $contentInfo,
            )
        );

        return new Item($location, $contentInfo, 'Some name');
    }
}