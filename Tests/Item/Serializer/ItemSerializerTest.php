<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Serializer;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Bundle\ContentBrowserBundle\Config\Configuration;
use Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider\ColumnProviderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\EzContent\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\ItemSerializerHandler;
use PHPUnit\Framework\TestCase;
use DateTimeZone;
use DateTime;

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
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer::serializeItem
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

        self::assertEquals(
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer::serializeItems
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

        self::assertEquals(
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer::serializeLocation
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

        self::assertEquals(
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializer::serializeLocations
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

        self::assertEquals(
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
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
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
